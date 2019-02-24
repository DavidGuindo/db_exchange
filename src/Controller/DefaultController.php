<?php
namespace App\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Validator\Constraints\Date;
use App\Entity\Category;
use App\Entity\Message;
use App\Entity\Service;
use App\Entity\Users;


/**
 * @Route("/")
 */
class DefaultController extends Controller {
   
	/**
	 * @Route("/", name="index")
	 */
	public function index(){
		//descargamos todos los servicios que tenemo en base de datos para mostrar en la vista
		$repositoryService = $this->getDoctrine()->getRepository(Message::class);	
		// Descargamos todos los servicios

		//$all_services = $repositoryService->findAll();
		return $this->render('index.html.twig');		
	}

	/**
	 * @Route("/AreaPrivada", name="AreaPrivada")
	 */
	public function areaPrivada(){
		
		return $this->render('privada.html.twig');		
	}


	/**
	 * @Route("/createService", name="createService")
	 * Crear servicios
	 */
	public function createService(){
		$entityManager = $this->getDoctrine()->getManager();
		$repositoryCategory = $this->getDoctrine()->getRepository(Category::class);;
		// buscamos la categoria por la id que hemos recibido
		$data['category']->$repositoryCategory->findOneById($_POST['id_category']);
		$data['name']->$_POST['name'];
		$data['img']->$_POST['img'];

		// creamos objeto
		$new_serice = new Service($data);

		// subimos a base de datos
		$entityManager->persist($new_serice);
		$entityManager->flush();
	}

	/**
	 * @Route("/createCategory", name="createCategory")
	 * Crear servicios
	 */
	public function createCategory(){
		$entityManager = $this->getDoctrine()->getManager();

		$data['name']->$_POST['name'];
		$data['img']->$_POST['img'];
		
		//creamos objeto
		$new_category = new Category($data);
		
		// subimos a base de datos
		$entityManager->persist($new_serice);
		$entityManager->flush();
	}

	/**
	 * @Route("/createMessage", name="createMessage")
	 * CREAR MENSAJE
	 */
	public function createMessage(){
		$entityManager = $this->getDoctrine()->getManager();

		$data['userSend']->$_POST['userSend'];
		$data['userReciving']->$_POST['userReciving'];
		$data['bodyMessage']->$_POST['bodyMessage'];
		
		//creamos objeto
		$new_message = new Message($data);
		
		// subimos a base de datos
		$entityManager->persist($new_message);
		$entityManager->flush();


	}
}