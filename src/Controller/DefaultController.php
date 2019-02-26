<?php
namespace App\Controller;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Validator\Constraints\Date;
use App\Entity\Users;
use App\Entity\Message;
use App\Entity\Service;
use App\Entity\Category;
use App\Entity\Request;
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

		if($user == "anon."){ //USUARIO NO LOGEADO

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

			
		}else{

		$token = $this->get('security.token_storage')->getToken();
		$user = $token->getUser();

		$repositoryCity = $this->getDoctrine()->getRepository(City::class);

		$repositoryCity = $this->getDoctrine()->getRepository(City::class);

		$ciudad = $repositoryCity->findOneByName($user->getCity());

		$servicios = $ciudad->getServices();

		$repositoryCategory = $this->getDoctrine()->getRepository(Category::class);
		$categorias = $repositoryCategory->findAll();
		
		return $this->render('index.html.twig', ['all_services'=>$servicios,
			'all_categories'=>$categorias]);

		}
		
	}

	

	/**
     * @Route("/logout", name="app_logout")
     */
	public function salir(Request $request): Response
	{
		return $this->index($request);


	}

	/**
	 * @Route("/areaprivada", name="areaprivada")
	 */
	public function areaprivada(){
		//Cogemos los repositorios
		$repositoryCategory = $this->getDoctrine()->getRepository(Category::class);
		$repositoryUsers = $this->getDoctrine()->getRepository(Users::class);
		$repositoryRequest = $this->getDoctrine()->getRepository(Request::class);
		$repositoryCity = $this->getDoctrine()->getRepository(City::class);
		$repositoryMessage = $this->getDoctrine()->getRepository(Message::class);

		// Usuario logeado
		$token = $this->get('security.token_storage')->getToken();
		$user = $token->getUser();
		

		// Descargamos todos las categorias, usuario, solicitudes y ciudades
		$all_category = $repositoryCategory->findAll();
		$all_users = $repositoryUsers->findAll();
		$all_city = $repositoryCity->findAll();
		$all_request = $repositoryRequest->findAll();

		// Buscamos los mensajes recibidos del usuario
		$allMessageRecivingUser = $repositoryMessage->findByUserReciving($user->getId());

		return $this->render('privada.html.twig', ['all_category'=>$all_category, 'all_users'=>$all_users, 'allMessageRecivingUser'=>$allMessageRecivingUser, 'all_city'=>$all_city, 'all_request'=>$all_request]);		
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
		$data['time']=$_POST['time'];

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

		return $this->redirectToRoute("areaprivada");
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

		return $this->redirectToRoute("areaprivada");

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

		return $this->redirectToRoute("areaprivada");

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

		return $this->redirectToRoute("areaprivada");
	}


	/**
	 * @Route("/buscador", name="buscar")
	 * CREAR MENSAJE
	 */
	public function filtro(){

		if($_POST['ciudadElegida'] != "0" && $_POST['categoriaElegida'] != "0"){

			$repositoryCity = $this->getDoctrine()->getRepository(City::class);

			$ciudad = $repositoryCity->findOneByName($_POST['ciudadElegida']);

			$todosServicios = $ciudad->getServices();



			foreach ($todosServicios as $key => $value) {

				if($value->getCategory()->getName() == $_POST['categoriaElegida']){
					$servicios[] = $todosServicios[$key];
					
				}
			}

		}

		if($_POST['ciudadElegida'] == "0" && $_POST['categoriaElegida'] != "0"){

			$repositoryCategory = $this->getDoctrine()->getRepository(Category::class);

			$categoria = $repositoryCategory->findOneByName($_POST['categoriaElegida']);

			$servicios = $categoria->getServices();

		}

		if($_POST['ciudadElegida'] != "0" && $_POST['categoriaElegida'] == "0"){

			$repositoryCity = $this->getDoctrine()->getRepository(City::class);

			$ciudad = $repositoryCity->findOneByName($_POST['ciudadElegida']);

			$servicios = $ciudad->getServices();

		}

		if($_POST['ciudadElegida'] == "0" && $_POST['categoriaElegida'] == "0"){
			//descargamos todos los servicios que tenemo en base de datos para mostrar en la vista
			$repositoryService = $this->getDoctrine()->getRepository(Service::class);
		// Descargamos todos los servicios
			$servicios = $repositoryService->findAll();
		}
		

		

		$repositoryCity = $this->getDoctrine()->getRepository(City::class);
		$ciudades = $repositoryCity->findAll();

		$repositoryCategory = $this->getDoctrine()->getRepository(Category::class);
		$categorias = $repositoryCategory->findAll();
		
		return $this->render('index.html.twig', ['all_services'=>$servicios, 'all_cities'=>$ciudades,
			'all_categories'=>$categorias]);		
	}

	

	/**
	 * @Route("/editarPerfil", name="editar")
	 * CREAR MENSAJE
	 */
	public function editar(){

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

		$token = $this->get('security.token_storage')->getToken();
		$user = $token->getUser();

		$entityManager = $this->getDoctrine()->getManager();
		$entityManager->remove($user);

		return $this->redirectToRoute("index");


	}

	/**
	 * @Route("/sendRequest", name="sendRequest")
	 * METODO QUE ENVIA UNA SOLICITUD DE SERVICIO A UN USUARIO
	 */
	public function sendRequest(){
		$entityManager = $this->getDoctrine()->getManager();
		$repositoryService = $this->getDoctrine()->getRepository(Service::class);;

		// buscamos el servicio en base de datos con la id que tenemos
		$data['service'] = $repositoryService->find($_POST['serviceId']);

		// cogemos el usuario logeado
		$token = $this->get('security.token_storage')->getToken();
		$data['userRequest'] = $token->getUser();

		// creamos un mensaje con el servicio y el usuario
		$newRequest = new Request($data);

		$entityManager->persist($newRequest);
		$entityManager->flush();

		return $this->redirectToRoute("index");
	}


	/**
	 * @Route("/facturar", name="factura")
	 * METODO QUE RESTA LOS MINUTOS DEL DEMANDANTE Y SE LOS PASA AL PRESTADOR DEL SERVICIO
	 */
	public function facturaServicio(){

												//servicioTime sera el tiempo del servicio realizado.
			$this->setTime(parseInt($this->getTime()) - parseInt($_POST['servicioTime']));		

			$repositoryUsers = $this->getDoctrine()->getRepository(Users::class);
			$prestador = $repositoryUsers->find($_POST['prestadorId']);//sacamos al prestador del servicio
			$prestador->setTime(parseInt($prestador->getTime()) - parseInt($_POST['servicioTime']));
																		//y aumentamos su tiempo
		

			return $this->redirectToRoute("index"); //redirección a index por defecto, eres libre de modificarlo 											david

	}


}