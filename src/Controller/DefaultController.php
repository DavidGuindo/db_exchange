<?php
namespace App\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Validator\Constraints\Date;
use App\Entity\Request;


/**
 * @Route("/")
 */
class DefaultController extends Controller {
   
	/**
	 * @Route("/", name="index")
	 */
	public function index(){
		
		return $this->render('index.html.twig');		
	}



	/**
	 * @Route
	 */


}