<?php

namespace dwpub\coffeeMakerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use dwpub\coffeeMakerBundle\Service\SearchAPI;
use dwpub\coffeeMakerBundle\Entity\CoffeeBean;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * CoffeMakerController.
 * Principal controller for this Coffee Maker Bundle.
 *
 * @package    dwpub
 * @subpackage coffeeMakerBundle
 * @author     mmduarte
 */
class CoffeMakerController extends Controller
{	
	/**
	 * Index Action, renders the index page
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
    public function indexAction()
    {
        return $this->render('dwpubcoffeeMakerBundle:CoffeMaker:index.html.twig');
    }
    
    /**
     * Search Action, performs the search using Google Custom API.
     * Gets search_text from post request.
     * Uses SearchAPI service to perform the search.
     * Renders the index page with founded images.
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function searchAction()
    {    	
    	$request = $this->getRequest();   
    	$search_text = $request->request->get('search_text');
    	
    	$searcher = new SearchAPI();
    	$images = $searcher->search($search_text);
    	
    	return $this->render('dwpubcoffeeMakerBundle:CoffeMaker:index.html.twig', array('images' => $images, 'search_text' => $search_text));
    }
        
    /**
     * Save Images Action, save coffee beans images choosen by user to DB.
     * Renders Coffee selection page with this coffee beans.
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function saveImagesAction()
    {    
    	$request = $this->getRequest();
    	$images = $request->request->get('images');
    	$coffeeBeansRequest = explode(',', $images);    	
    	
    	$em = $this->getDoctrine()->getEntityManager();
    	foreach ($coffeeBeansRequest as $coffeeBeanRequest) 
    	{
    		$coffeeBean = new CoffeeBean();
    		$coffeeBean->setLink($coffeeBeanRequest);
    		$em->persist($coffeeBean);
    	}
    	$em->flush();
    	
    	return $this->render('dwpubcoffeeMakerBundle:CoffeMaker:grinder.html.twig', array('coffeeBeans' => $coffeeBeansRequest));
    }
    
    /**
     * Add to Grinder Action, add the bean and quantity chosen by user to memcache.
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addToGrinderAction()
    {    	
    	$request = $this->getRequest();
    	$coffeeBeanLink = $request->request->get('coffeeBeanLink');
    	$quantity = $request->request->get('quantity');
    	if ($quantity == '')
    	{
    		$quantity = '1';
    	}
    	
    	$this->get('memcache.default')->set($coffeeBeanLink, $quantity);
    	
    	return new Response();
    }
        
    /**
     * Update Grinder Action, update grinder with the beans and quantity chosen stored in memcache.
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function updateGrinderAction()
    {    	
    	$request = $this->getRequest();
    	$coffeeBeanLink = $request->request->get('coffeeBeanLink');
    	
    	$quantity = $this->get('memcache.default')->get($coffeeBeanLink);
    	
    	return new JsonResponse(array('quantity' => $quantity, 'coffeeBeanLink' => $coffeeBeanLink));
    }
    
    /**
     * Grinder Action, perform the grinder functionality.
     * Gets coffee beans links from memcache to delete contents from the DB.
     * Also flush memcache storage.
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function grindAction()
    {
    	$em = $this->getDoctrine()->getEntityManager();
    	$repo = $em->getRepository('dwpubcoffeeMakerBundle:CoffeeBean');
    	
    	$keys = $this->get('memcache.default')->getAllKeys();
    	foreach ($keys as $key)
    	{
			$coffeeBean = $repo->findOneBy(array('link' => $key));
    		$em->remove($coffeeBean);    		
    		$em->flush();
    	}
    	
    	$this->get('memcache.default')->flush();
    	
    	return new Response();
    }
    
    /**
     * Thanks Action, render the thanks page.
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function thanksAction()
    {
    	return $this->render('dwpubcoffeeMakerBundle:CoffeMaker:thanks.html.twig');
    }
}