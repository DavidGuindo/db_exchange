<?php
namespace App\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
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
		
		//descargamos todos los servicios que tenemo en base de datos para mostrar en la vista
		$repositoryService = $this->getDoctrine()->getRepository(Service::class);
		// Descargamos todos los servicios
		$all_services = $repositoryService->findAll();
		
		return $this->render('index.html.twig', ['all_services'=>$all_services, 'userLogged'=>$user]);		
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
			$requestModify->setAccept(true);
		} else if (isset($_POST['deny'])){
			$requestModify->setAccept(false);
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

}