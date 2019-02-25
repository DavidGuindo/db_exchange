<?php
namespace App\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Validator\Constraints\Date;
use App\Entity\Users;
use App\Entity\Message;
use App\Entity\Service;
use App\Entity\Category;
use App\Entity\City;


// $token = $this->get('security.token_storage')->getToken();
// $user = $token->getUser(); OBTENER USUARIO LOGEADO.



/**
 * @Route("/")
 */
class DefaultController extends Controller {

	/**
	 * @Route("/", name="index")
	 */
	public function index(){
		// Con esto descargamos el usuario logeado
		$token = $this->get('security.token_storage')->getToken();
 		$user = $token->getUser();
		
		//descargamos todos los servicios que tenemo en base de datos para mostrar en la vista
		$repositoryService = $this->getDoctrine()->getRepository(Service::class);
		// Descargamos todos los servicios
		$all_services = $repositoryService->findAll();

		$repositoryCity = $this->getDoctrine()->getRepository(City::class);
		$ciudades = $repositoryCity->findAll();

		$repositoryCategory = $this->getDoctrine()->getRepository(Category::class);
		$categorias = $repositoryCategory->findAll();
		

		return $this->render('index.html.twig', ['all_services'=>$all_services, 'all_cities'=>$ciudades,
			'all_categories'=>$categorias]);		

		return $this->render('index.html.twig', ['all_services'=>$all_services, 'userLogged'=>$user]);		

	}

	/**
     * @Route("/logout", name="app_logout")
     */

	public function salir(Request $request): Response
	{
		return $this->index($request);


	}

	/**
	 * @Route("/AreaPrivada", name="AreaPrivada")
	 */
	public function areaPrivada(){
		//Cogemos los repositorios
		$repositoryCategory = $this->getDoctrine()->getRepository(Category::class);
		$repositoryUsers = $this->getDoctrine()->getRepository(Users::class);
		$repositoryCity = $this->getDoctrine()->getRepository(City::class);
		$repositoryMessage = $this->getDoctrine()->getRepository(Message::class);
	
		// Usuario logeado
		$token = $this->get('security.token_storage')->getToken();
		$user = $token->getUser();
		

		// Descargamos todos las categorias, usuario y ciudades
		$all_category = $repositoryCategory->findAll();
		$all_users = $repositoryUsers->findAll();
		$all_city = $repositoryCity->findAll();

		// Buscamos los mensajes recibidos del usuario
		$allMessageRecivingUser = $repositoryMessage->findByUserReciving($user->getId());

		return $this->render('privada.html.twig', ['all_category'=>$all_category, 'all_users'=>$all_users, 'allMessageRecivingUser'=>$allMessageRecivingUser, 'all_city'=>$all_city]);		
	}

	/**
	 * @Route("/createService", name="createService")
	 * Crear servicios
	 */
	public function createService(){
		$entityManager = $this->getDoctrine()->getManager();
		$repositoryCategory = $this->getDoctrine()->getRepository(Category::class);;
		$repositoryCity = $this->getDoctrine()->getRepository(City::class);;
		// buscamos la categoria y la ciudad por la id que hemos recibido
		$data['city']=$repositoryCity->findOneById($_POST['city']);
		$data['category']=$repositoryCategory->findOneById($_POST['category']);
		$data['name']=$_POST['name'];

		move_uploaded_file($_FILES['img']['tmp_name'], $_FILES['img']['name']);
		$imagen = $_FILES['img']['name'];
		$data['img']=$imagen;

		// cogemos el usuario logeado para crear el servicio
		$token = $this->get('security.token_storage')->getToken();
		$data['userOffer'] = $token->getUser();

		// creamos objeto
		$new_service = new Service($data);

		// subimos a base de datos
		$entityManager->persist($new_service);
		$entityManager->flush();

		return $this->redirectToRoute("AreaPrivada");
	}

	/**
	 * @Route("/createCategory", name="createCategory")
	 * Crear servicios
	 */
	public function createCategory(){
		$entityManager = $this->getDoctrine()->getManager();

		$data['name']=$_POST['name'];
		move_uploaded_file($_FILES['img']['tmp_name'], $_FILES['img']['name']);
		$imagen = $_FILES['img']['name'];
		$data['img']=$imagen;
		
		//creamos objeto
		$new_category = new Category($data);
		
		// subimos a base de datos
		$entityManager->persist($new_category);
		$entityManager->flush();

		return $this->redirectToRoute("AreaPrivada");

	}

	/**
	 * @Route("/createMessage", name="createMessage")
	 * CREAR MENSAJE
	 */
	public function createMessage(){
		$entityManager = $this->getDoctrine()->getManager();
		$repositoryUsers = $this->getDoctrine()->getRepository(Users::class);;

		// Usuario logeado
		$token = $this->get('security.token_storage')->getToken();
		$user = $token->getUser();

		//buscamos en base de datos el usuario que recibe el mensaje mediante su id
		$data['userReciving'] = $repositoryUsers->find($_POST['userReciving']);
		$data['userSend']=$user;
		$data['bodyMessage']=$_POST['bodyMessage'];
		
		//creamos objeto
		$new_message = new Message($data);
		
		echo $new_message->getCheckRead();
		echo "<br>";
		echo $new_message->getUserReciving()->getEmail();
		echo "<br>";
		echo $new_message->getUserSend()->getEmail();
		// subimos a base de datos
		$entityManager->persist($new_message);
		$entityManager->flush();

		return $this->redirectToRoute("AreaPrivada");

	}

	/**
	 * @Route("/createCity", name="createCity")
	 * CREAR CIUDAD
	 */
	public function createCity(){
		$entityManager = $this->getDoctrine()->getManager();
		$data['name']=$_POST['name'];
		
		//creamos objeto
		$new_city = new City($data);
		
		// subimos a base de datos
		$entityManager->persist($new_city);
		$entityManager->flush();

		return $this->redirectToRoute("AreaPrivada");
	}


	/**
	 * @Route("/buscador", name="buscar")
	 * CREAR MENSAJE
	 */
	public function filtro(){

		var_dump($_POST);
		die;

		$repositoryCity = $this->getDoctrine->getRepository(City::class);

		$ciudad = $repositoryCity->findOneByName($_POST['ciudadElegida']);

		$todosServicios = $ciudad->getServices();

		foreach ($todosServicios as $key => $value) {
			if($value->getCategory == $_POST['categoriaElegida']){
				$servicios[] = $todosServicios[$key];
			}
		}

		$repositoryCity = $this->getDoctrine()->getRepository(City::class);
		$ciudades = $repositoryCity->findAll();

		$repositoryCategory = $this->getDoctrine()->getRepository(Category::class);
		$categorias = $repositoryCategory->findAll();
		
		return $this->render('index.html.twig', ['all_services'=>$servicios, 'all_cities'=>$ciudades,
			'all_categories'=>$categorias]);		
	}

	/**
	 * @Route("/editarContacto", name="editar")
	 * CREAR MENSAJE
	 */
	public function editarPerfil(){

		$token = $this->get('security.token_storage')->getToken();
		$user = $token->getUser(); 

		if(isset($_POST['modificar'])){

			if($_POST['nuevoName'] != ""){

				$user->setName($_POST['nuevoName']);

			}

			if($_POST['nuevoLastName'] != ""){

				$user->setLastName($_POST['nuevoLastName']);

			}

			if($_POST['nuevoCity'] != ""){

				$user->setCity($_POST['nuevoCity']);

			}

			if ($_FILES['nuevoImg']['name'] != null) {

				move_uploaded_file($_FILES['nuevoImg']['tmp_name'], $_FILES['nuevoImg']['name']);
				$imagen = $_FILES['nuevoImg']['name'];
				$user->setImg($imagen);

			}



			$entityManager = $this->getDoctrine()->getManager();
			$entityManager->merge($user);
			$entityManager->flush();

		}

		return $this->render('editarContacto.html.twig', ['userLogeado'=>$user]);

	}

	/**
	 * @Route("/baja", name="darDeBaja")
	 * CREAR MENSAJE
	 */
	public function darDeBaja()
	{
		
	}

	/**
	 * @Route("/sendRequest", name="sendRequest")
	 * METODO QUE ENVIA UNA SOLICITUD DE SERVICIO A UN USUARIO
	 */
	public function sendRequest(){
		$entityManager = $this->getDoctrine()->getManager();
		$repositoryService = $this->getDoctrine()->getRepository(Service::class);;

		// buscamos el servicio en base de datos con la id que tenemos
		$service = $repositoryService->find($_POST['serviceId']);

		// cogemos el usuario logeado
		$token = $this->get('security.token_storage')->getToken();
		$data['userSend'] = $token->getUser();


		$data['userReciving'] = $service->getUserOffer();
		$data['bodyMessage'] = "El usuario: ".$data['userSend']->getEmail()." solicita tu servicio de ".$service->getName()."";

		// creamos un mensaje con el servicio y el usuario
		$new_message = new Message($data);

		$entityManager->persist($new_message);
		$entityManager->flush();

		return $this->redirectToRoute("index");
	}


}