<?php

namespace ArusRequestBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

use ArusProjectBundle\Entity\ArusProject;
use ArusRequestBundle\Entity\ArusRequest;

use Actarus\Utils;


class ServiceController extends Controller
{
	protected $em;

	protected $container;

	protected $router;

	protected $formFactory;

	protected $templating;


	public function __construct( $entityManager, $container, $router, $formFactory, $templating ) {
		$this->em          = $entityManager;
		$this->container   = $container;
		$this->router      = $router;
		$this->formFactory = $formFactory;
		$this->templating  = $templating;
	}


	public function search( $data=[], $page=1, $limit=-1 )
	{
		if( $limit < 0 ) {
			$limit = $this->getParameter('results_per_page');
		}
		$offset = $limit * ($page-1);

		$t_request = $this->em->getRepository('ArusRequestBundle:ArusRequest')->search( $data, $offset, $limit );
		/*if( is_array($t_request) ) {
			foreach ($t_request as $h) {
				$this->get('app')->getRelatedEntityObject( $h, true );
			}
		}*/

		return $t_request;
	}
	
	
	public function exist( $sign, $return_object=false )
	{
		$t_params = ['sign'=>[$sign,'=']];

		if( $return_object ) {
			$offset = 1;
		} else {
			$offset = -1;
		}

		$exist = $this->search( $t_params, $offset );

		if( !$return_object ) {
			return $exist;
		} elseif( is_array($exist) && count($exist) ) {
			return $exist[0];
		} else {
			return false;
		}
	}

	/*
	public function create( $project, $domain, $name, $recon=true )
	{
		if( !Utils::isSubdomain($name) ) {
			return false;
		}

		$host = new ArusHost();
		$host->setProject( $project );
		$host->setDomain( $domain );
		$host->setName( $name );

		$em = $this->em;
		$em->persist( $host );
		$em->flush( $host );

		if( $recon ) {
			$this->get('app')->recon( $host, 'host' );
		}

		return $host;
	}
	*/
	
	public function import( $project, $sf, $format, $recon )
	{
		set_time_limit( 0 );

		switch( $format ) {
			case 'raw_txt';
				return $this->doImportRawTxt( $project, $sf, $recon );
			case 'bs_xml';
				return $this->doImportXml( $project, $sf, false, $recon );
			case 'bs_xml64';
				return $this->doImportXml( $project, $sf, true, $recon );
			default:
				return false;
		}
	}
	
	
	private function doImportRawTxt( $project, $sf, $recon )
	{
		$cnt = 0;
		$rq = $this->getParameter('request');
		$t_urls = file( $sf, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES );
		$em = $this->getDoctrine()->getManager();
		
		foreach( $t_urls as $url )
		{
			if( trim($url) == '' ) {
				continue;
			}
			
			$t_info = parse_url( $url );
			//var_dump( $t_info );
			
			$r = new ArusRequest();
			$r->setUrl( $url );
			$r->setSign( $this->createSign($r->getUrl()) );
			$r->setProject( $project );
			$r->setHost( $t_info['host'] );
			$r->setProtocol( $t_info['scheme'] );
			$r->setMethod( $rq['default_method'] );
			$r->setPath( $t_info['path'] );
			
			if( $r->getProtocol() == 'https' ) {
				$r->setPort( 443 );
			} else {
				$r->setPort( $rq['default_port'] );
			}

			if( isset($t_info['query']) ) {
				$r->setQuery( $t_info['query'] );
			}

			$r->setName( $r->getHost().$r->getPath() );

			if( !$this->exist($r->getSign(),true) )
			{
				$cnt++;
				$em->persist( $r );
				$em->flush();
				
				if( $recon ) {
					$this->get('app')->recon( $r, 'request' );
				}
			}
		}
		
		return $cnt;
	}
	
	
	private function doImportXml( $project, $sf, $base64, $recon )
	{
		$cnt = 0;
		$query = null;
		$em = $this->getDoctrine()->getManager();
		
		$dom = new \DOMDocument();
		$dom->loadXML( file_get_contents($sf) );
		
		$items = $dom->getElementsByTagName( 'item' );
		
		foreach( $items as $i )
		{
			$r = new ArusRequest();
			$r->setProject( $project );
			
			foreach( $i->childNodes as $n ) {
				switch ( $n->nodeName ) {
					case 'url':
						$r->setUrl( $n->nodeValue );
						break;
					case 'host':
						$r->setHost( $n->nodeValue );
						break;
					case 'port':
						$r->setPort( $n->nodeValue );
						break;
					case 'protocol':
						$r->setProtocol( $n->nodeValue );
						break;
					case 'method':
						$r->setMethod( $n->nodeValue );
						break;
					case 'request':
						$query = $n->nodeValue;
						if( $base64 ) {
							$query = base64_decode( $query );
						}
						break;
					default:
						break;
				}
				
			}
			
			$r->setSign( $this->createSign($r->getUrl()) );
			$t_info = parse_url( $r->getUrl() );
			$r->setPath( $t_info['path'] );
			if( isset($t_info['query']) ) {
				$r->setQuery( $t_info['query'] );
			}
			
			if( !$this->exist($r->getSign(),true) ) {
				$cnt++;
				$em->persist( $r );
				$em->flush();
				
				if( $recon ) {
					$this->get('app')->recon( $r, 'request' );
				}
			}
		}
		
		return $cnt;
	}
	
	
	public function createSign( $url )
	{
	    $url = urldecode( trim($url,' /') );
	    
	    $sign = '';
	    $t_params = [];
	    $t_parse = parse_url( $url );
	    //var_dump( $t_parse );
	    
	    if( isset($t_parse['query']) ) {
	        $t_query = explode( '&', $t_parse['query'] );
	        foreach( $t_query as $q ) {
	            if( strstr($q,'=') ) {
	                $tmp = explode( '=', $q );
	                $t_params[ $tmp[0] ] = $tmp[1];
	            } else {
	                $t_params[ $q ] = '';
	            }
	        }
	        ksort( $t_params );
	    }
	    //var_dump( $t_parse );
	    //var_dump( $t_params );
	
	    $str = str_replace( 's', '', $t_parse['scheme'] );
	    $str .= $t_parse['host'];
	    $str .= $t_parse['path'];
	
	    foreach( $t_params as $k=>$v ) {
	        $str .= $k;
	        $str .= $v;
	    }
	    
	    //echo $str."\n";
	    $sign = md5( $str );
	    
	    return $sign;
	}
}
