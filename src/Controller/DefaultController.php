<?php
namespace App\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Validator\Constraints\Date;
use App\Entity\Users;
use App\Entity\Message;
use App\Entity\Service;
use App\Entity\Category;
use App\Entity\City;

// $token = $this->get('security.token_storage')->getToken();
// 		$user = $token->getUser();                         			OBTENER USUARIO LOGEADO.



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
    public function salir(Request $request): Response
    {
        return $this->index($request);

    }

	/**
	 * @Route("/AreaPrivada", name="AreaPrivada")
	 */
	public function areaPrivada(){
		//descargamos todos las categorias y usuarios que tenemos en base de datos para mostrar en la vista
		$repositoryCategory = $this->getDoctrine()->getRepository(Category::class);
		$repositoryUsers = $this->getDoctrine()->getRepository(Users::class);
		$repositoryMessage = $this->getDoctrine()->getRepository(Message::class);
		// Descargamos todos las categorias y usuarios
		$all_category = $repositoryCategory->findAll();
		$all_users = $repositoryUsers->findAll();
		//$all_message = $repositoryMessage->findByUserSend($IDUSUARIO LOGEADO);
		return $this->render('privada.html.twig', ['all_category'=>$all_category, 'all_users'=>$all_users/*, 'all_message'=>$all_message*/]);		
	}


	/**
	 * @Route("/createService", name="createService")
	 * Crear servicios
	 */
	public function createService(){
		$entityManager = $this->getDoctrine()->getManager();
		$repositoryCategory = $this->getDoctrine()->getRepository(Category::class);;
		// buscamos la categoria por la id que hemos recibido
		$data['category']=$repositoryCategory->findOneById($_POST['category']);
		$data['name']=$_POST['name'];

		move_uploaded_file($_FILES['img']['tmp_name'], $_FILES['img']['name']);
        $imagen = $_FILES['img']['name'];
		$data['img']=$imagen;

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

		$data['userSend']=$_POST['userSend'];
		$data['userReciving']=$_POST['userReciving'];
		$data['bodyMessage']=$_POST['bodyMessage'];
		
		//creamos objeto
		$new_message = new Message($data);
		
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
	 * @Route("/list", name="createCity")
	 * 
	 */
}