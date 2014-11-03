<?php

namespace dwpub\coffeeMakerBundle\Service;

/**
 * SearchAPI
 * Utility class used to search thru Google Search API.
 *
 * @package    dwpub
 * @subpackage coffeeMakerBundle
 * @author     mmduarte
 */
class SearchAPI
{       
	/**
	 * Search method.
	 * @param $search_text query.
	 * @return $images results from the search.
	 */
    public function search($search_text)
    {    	
    	$images = array();
    	
    	$appName = 'PhpTestHome';
    	$devKey = 'AIzaSyDRZX0HfSTmN1KqEflVaaSZq6UKL6LoD8Y';
    	$cx = '012706412836880192375:itinjbuv4ca';
    	
    	if ($search_text != '') 
    	{    		    	
    		session_start();
    		$client = new \Google_Client();    		
    		$client->setApplicationName( $appName );
    		$client->setDeveloperKey($devKey);    		
    		$search = new \Google_Service_Customsearch($client);    		
    		$result = $search->cse->listCse($search_text, array(    				
    			'searchType' => 'image',
    			'cx' => $cx,
    			'hq' => 'coffee bean',
    			'num' => '10',
    		));
    		    	
    		$images = array($result->getSearchInformation()->totalResults);
	    	foreach($result->getItems() as $item)
	    	{	    		 
	    		$imageTitle = $item->getTitle();
	    		$imageLink = $item->getLink();	    		
	    		if (strstr($imageLink,'http://'))
	    		{
	    			$images[$imageTitle] = $imageLink;
	    		}
	    	}
    	}
    	return $images;
    }
}