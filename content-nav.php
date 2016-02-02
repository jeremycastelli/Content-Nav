<?php
/*
Plugin Name: Content Nav
Description: Create a content navigation based on headings
Version: 1.0.1
Author: Jeremy Castelli
Author URI: http://jeremycastelli.com
Inspiration: Andi Dittrich <http://andidittrich.de>

*/

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

function jelli_get_content_menu($args){
	$cm = new Jelli_Content_Nav($args);
	return $cm->getNav();
}

class Jelli_Content_Nav
{
	private $nav_items = array();
	private $headingLimit;
	private $cpt = 0;

	public function __construct($args = null){
		$defaults = array(
			'limit' => 2,
		);

		$r = wp_parse_args( $args, $defaults );
		$this->headingLimit = array(2,$r['limit']);

		add_filter('the_content', array($this,'findHeadings'), 9999, 1);
		apply_filters('the_content',get_the_content());

	}

	public function findHeadings($content){
	
		return preg_replace_callback('/<h([1-6]).*?>(.*)<\/h[1-6]>/Uu',array($this,'addClasses'), $content );
	
	}

	private function addClasses($matches) {

		$this->cpt++;

		$className = preg_replace('/[^a-z1-9]+/u', '-', strtolower($matches[2]) ).'-'.$this->cpt;
		    
		if (in_array(intval($matches[1]), $this->headingLimit)){
		    $this->nav_items[] = array(
		            'level' => intval($matches[1]),
		            'class' => $className,
		            'title' => $matches[2]
		    );
		}
		    
		return '<h'.$matches[1].' class="'.$className.'">'.trim($matches[2]).'</h'.$matches[1].'>';
	
	}


	public function getNav(){
		
		if ( count($this->nav_items) > 0 ){
		   
			// get current level
			$firstElement = array_shift($this->nav_items);
			$currentLevel = $firstElement['level'];

			$output = '<nav class="content-nav">';

			// generate startup structure / first element
			for ($i=0;$i<($currentLevel-1);$i++){
			   $output .= '<ul><li>';
			}
			$output .= '<a href="#'. $firstElement['class']. '">'. $firstElement["title"]. '</a>';

			foreach ($this->nav_items as $element){
			   // same level ?
			   if ($currentLevel == $element['level']){
			       $output .= '</li><li><a href="#'. $element['class']. '">'. $element["title"]. '</a>'. "\n";
			   
			   // higher level
			   }else if ($currentLevel > $element['level']){
			       // close structures
			       for ($i=0;$i<($currentLevel-$element['level']);$i++){
			           $output .= '</li></ul>';
			       }
			       
			       // create node
			       $output .= '</li><li><a href="#'. $element['class']. '">'. $element["title"]. '</a>'. "\n";
			       
			   // lower level    
			   }else{
			       // generate startup structure / first element
			       for ($i=0;$i<($element['level']-$currentLevel);$i++){
			           $output .= '<ul><li>';
			       }
			       
			       // create node
			       $output .= '<a href="#'. $element['class']. '">'. $element["title"]. '</a>'. "\n";
			   }
			   
			   // store current elements level
			   $currentLevel = $element['level'];
			}

			// close structure
			for ($i=0;$i<($currentLevel-1);$i++){
			   $output .= '</li></ul>';
			}
			$output.="</nav>"
			return $output;
		}
		else{
			return '';
		}
	}
}

