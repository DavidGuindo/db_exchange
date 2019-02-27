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
use App\Entity\Contacto;


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
			$repositoryCategory = $this->getDoctrine()->getRepository(Category::class);

			$categorias = $repositoryCategory->findAll();
			$ciudad = $repositoryCity->findOneByName($user->getCity());
			
			

			if($ciudad->getServices() == NULL) {
				$message = 'No hay resultados disponibles temporalmente';
				return $this->render('index.html.twig', [$message,'all_categories'=>$categorias, 'user' => $user]);
			} else {

			$servicios = $ciudad->getServices();
			return $this->render('index.html.twig', ['all_services'=>$servicios,'all_categories'=>$categorias, 'user' => $user]);
			}
		}
		
		return $this->render('index.html.twig', ['all_services'=>$all_services, 'all_cities'=>$ciudades, 'all_categories'=>$categorias, 'userLogged'=>$user]);		
	}

	/**
     * @Route("/logout", name="app_logout")
     */
    public function salir(Request $request): Response {
        return $this->index($request);
	}

	/**
	 * @Route("/areaprivada", name="areaprivada")
	 */
	public function areaprivada(){

		if(! $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY')){
		}
		//Cogemos los repositorios
		$repositoryCategory = $this->getDoctrine()->getRepository(Category::class);
		$repositoryUsers = $this->getDoctrine()->getRepository(Users::class);
		$repositoryRequest = $this->getDoctrine()->getRepository(Request::class);
		$repositoryCity = $this->getDoctrine()->getRepository(City::class);
		// Usuario logeado
		$token = $this->get('security.token_storage')->getToken();
		$user = $token->getUser();

		// solicitudes del usuario
		$allRequest = $user->getRequests();
		
		// Mensajes del usuario
		$allMessage = $user->getMessages();

		// Descargamos todos las categorias, usuario, solicitudes y ciudades
		$all_category = $repositoryCategory->findAll();
		$all_users = $repositoryUsers->findAll();
		$all_city = $repositoryCity->findAll();

		return $this->render('privada.html.twig', ['all_category'=>$all_category, 'all_users'=>$all_users, 'allMessage'=>$allMessage, 'all_city'=>$all_city, 'all_request'=>$allRequest]);		
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
	 * @Route("/contacto", name="contacto")
	 */
	public function contacto(){
		
		return $this->render('contacto.html.twig');		
	}

	/**
	 * @Route("/contactoEnviado", name="contactoEnviado")
	 */
	
	public function contactoEnviado(){
		$today = date("F j, Y, g:i a");                 // March 10, 2001, 5:16 pm
        if(isset($_POST['contactoEnv'])) {
			$contact = new Contacto($_POST['email'], $_POST['name'], $_POST['mensaje'], $today);
			$manager = $this->getDoctrine()->getManager();
			$manager->persist($contact);
			$manager->flush($contact);
		}
		return $this->contacto();
	}

	/**
	 * @Route("/contactoLeido", name="contactoLeido")
	 */
	// maybe
	public function contactoLeido(Contacto $contact){
			$value=true;
			$contact->setLeido($value);
			$manager = $this->getDoctrine()->getManager();
            $manager->merge($contact);
            $manager->flush($contact);
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
	 * @Route("/acceptRequest", name="acceptRequest")
	 * METODO QUE ACEPTA LA SOLICITUD DE UN SERVICIO
	 */
	public function acceptRequest(){
		$entityManager = $this->getDoctrine()->getManager();
		$repositoryRequest = $this->getDoctrine()->getRepository(Request::class);
		
		// buscamos la solicitud en base de datos con la id recibida
		$requestModify = $repositoryRequest->find($_POST['requestId']);

		//Comprobamos si hemos rechazado o aceptado la solicitud
		if(isset($_POST['accept'])){
			$requestModify->setAccept(1);
		} else if (isset($_POST['deny'])){
			$requestModify->setAccept(0);
		}

		// Lo modificamos en base de datos
		$entityManager->merge($requestModify);
		$entityManager->flush();

		return $this->redirectToRoute("areaprivada");

	}

	
	/**
	 * @Route("/finishRequest", name="finishRequest")
	 * METODO QUE ACEPTA LA SOLICITUD DE UN SERVICIO
	 */
	public function finishRequest(){
		$entityManager = $this->getDoctrine()->getManager();
		$repositoryRequest = $this->getDoctrine()->getRepository(Request::class);
		
		// buscamos la solicitud en base de datos con la id recibida
		$requestModify = $repositoryRequest->find($_POST['requestId']);

		//Marcamos el servicio como finalizado
		$requestModify->setFinish(true);

		// Lo modificamos en base de datos
		$entityManager->merge($requestModify);
		$entityManager->flush();

		return $this->redirectToRoute("areaprivada");

	}

	/**
	 * @Route("/buscador", name="buscar")
	 * CREAR MENSAJE
	 */
	public function filtro(){

		$token = $this->get('security.token_storage')->getToken();
		$user = $token->getUser();
		$repositoryCity = $this->getDoctrine()->getRepository(City::class);
		$repositoryCategory = $this->getDoctrine()->getRepository(Category::class);

		if($user == "anon."){

			if($_POST['ciudadElegida'] != "0" && $_POST['categoriaElegida'] != "0"){

				$ciudad = $repositoryCity->findOneByName($_POST['ciudadElegida']);

				$todosServicios = $ciudad->getServices();

				foreach ($todosServicios as $key => $value) {

					if($value->getCategory()->getName() == $_POST['categoriaElegida']){
						$servicios[] = $todosServicios[$key];
					}
			}

		}

		if($_POST['ciudadElegida'] == "0" && $_POST['categoriaElegida'] != "0"){

			$categoria = $repositoryCategory->findOneByName($_POST['categoriaElegida']);

			$servicios = $categoria->getServices();

		}

		if($_POST['ciudadElegida'] != "0" && $_POST['categoriaElegida'] == "0"){

			$ciudad = $repositoryCity->findOneByName($_POST['ciudadElegida']);

			$servicios = $ciudad->getServices();

		}

		if($_POST['ciudadElegida'] == "0" && $_POST['categoriaElegida'] == "0"){

			$servicios = $repositoryService->findAll();
		}

		$ciudades = $repositoryCity->findAll();

		$categorias = $repositoryCategory->findAll();
		
		return $this->render('index.html.twig', ['all_services'=>$servicios, 'all_cities'=>$ciudades,
			'all_categories'=>$categorias, 'userLogged'=>$user]);

		}else{

			if($_POST['categoriaElegida'] == "0"){

				return $this->redirectToRoute("index");
			}else{

				$categoria = $repositoryCategory->findOneByName($_POST['categoriaElegida']);

				$ciudad = $user->getCity();

				$categorias = $repositoryCategory->findAll();

				$todosServicios = $categoria->getServices();

				foreach ($todosServicios as $key => $value) {

					if($value->getCity()->getName() == $ciudad ){
						$servicios[] = $todosServicios[$key];
						
					}
				}

				return $this->render('index.html.twig', ['all_services'=>$servicios, 'all_categories'=>$categorias, 'user'=>$user]);
			}
		}
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

		return $this->redirectToRoute("app_logout");


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
		$prestador->setTime(parseInt($prestador->getTime()) - parseInt($_POST['servicioTime'])); //y aumentamos su tiempo
																	


		return $this->redirectToRoute("index"); //redirección a index por defecto, eres libre de modificarlo 											david

	}
}

