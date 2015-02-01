<?php

/**
 * SMVPE - Social Media Video Parse & Embed
 *
 * This class allows you to pass a source url from
 * a number of social media video wesbites and parse
 * it in order to embed the video associated with it.
 * 
 * SMVPE requires PHP Version 5.3 or higher.
 *
 * @author  Len Bradley <lenbradley@ninesphere.com>
 * @license http://www.php.net/license/3_01.txt PHP License 3.01
 * @link    http://www.ninesphere.com/lab/smvpe-php-class-parse-embed-social-media-videos/
 * @package smvpe_package
 * @version 1.0.33
 * 
 */

class SMVPE
{
    public      $source, $site, $options;
    protected   $sites;

    public function __construct( $source = '', $options = array() )
    {        
        $this->sites = $this->getSites();

        // Check if $source should be $options
        if ( is_array( $source ) && empty( $options ) ) {
            $options    = $source;
            $source     = '';
        }

        $this->setSource( $source );
        $this->setOptions( $options );
    }

    /**
     * Provides a way to access class without initiating a new SMVPE object first.
     * i.e. SMVPE::init( 'youtube.com/embed/6FWUjJF1ai0' )->embed();
     * 
     * @param string $source 
     * @param array $options 
     * @return SMVPE Object
     */
    public static function init( $source = '', $options = array() )
    {
        return new SMVPE( $source, $options );
    }

    /**
     * Returns a list of all compatible sites and data
     * associated with site.
     * 
     * @return array
     */
    protected function getSites()
    {
        $sites = array(
            'break' => array(
                'url'       => 'http://www.break.com',
                'embed'     => '//www.break.com/embed/{id}',
                'data'      => '',
                'extract'   => function ( $parsed = '' ) {

                    if ( isset( $parsed['path'] ) ) {
                        $parsed['path'] = explode( '/', $parsed['path'] );
                        $parsed['path'] = array_pop( $parsed['path'] );
                        $parsed['path'] = explode( '-', $parsed['path'] );
                        $parsed['path'] = array_pop( $parsed['path'] );

                        return $parsed['path'];
                    }

                    return $parsed;
                }
            ),
            'dailymotion' => array(
                'url'       => 'http://www.dailymotion.com',
                'embed'     => '//www.dailymotion.com/embed/video/{id}',
                'data'      => 'https://api.dailymotion.com/video/{id}',
                'extract'   => function ( $parsed = '' ) {

                    if ( isset( $parsed['path'] ) ) {
                        $parsed['path'] = explode( '/', $parsed['path'] );
                        $parsed['path'] = array_pop( $parsed['path'] );

                        if ( strpos( $parsed['path'], '_' ) !== false ) {
                            $parsed['path'] = explode( '_', $parsed['path'] );
                            $parsed['path'] = $parsed['path'][0];
                        }

                        return $parsed['path'];
                    }

                    return $parsed;
                }
            ),
            'metacafe' => array(
                'url'       => 'http://www.metacafe.com',
                'embed'     => '//www.metacafe.com/embed/{id}/',
                'data'      => 'http://www.metacafe.com/api/item/{id}/',
                'extract'   => function ( $parsed = '' ) {

                    if ( isset( $parsed['path'] ) ) {
                        $parsed['path'] = explode( '/', $parsed['path'] );

                        if ( isset( $parsed['path'][1] ) ) {
                            return $parsed['path'][1];
                        }
                    }

                    return $parsed;
                }
            ),
            'vimeo' => array(
                'url'       => 'https://vimeo.com/',
                'embed'     => '//player.vimeo.com/video/{id}',
                'data'      => 'https://vimeo.com/api/v2/video/{id}.json',
                'extract'   => function ( $parsed = '' ) {

                    if ( isset( $parsed['path'] ) ) {
                        $parsed['path'] = explode( '/', $parsed['path'] );
                        $parsed['path'] = array_pop( $parsed['path'] );

                        return $parsed['path'];
                    }

                    return $parsed;
                }
            ),
            'youtube' => array(
                'url'       => 'https://www.youtube.com/',
                'embed'     => '//www.youtube.com/embed/{id}',
                'data'      => 'https://gdata.youtube.com/feeds/api/videos/{id}?v=2&alt=jsonc',
                'extract'   => function ( $parsed = '' ) {

                    if ( isset( $parsed['query'] ) ) {
                        parse_str( $parsed['query'], $query );

                        if ( isset( $query['v'] ) ) {
                            return $query['v'];
                        }
                    }

                    if ( isset( $parsed['path'] ) ) {
                        $parsed['path'] = explode( '/', $parsed['path'] );
                        $parsed['path'] = array_pop( $parsed['path'] );

                        return $parsed['path'];
                    }

                    return $parsed;
                }
            )
        );

        return $sites;
    }

    /**
     * Searches for and returns a single site. If no site is found 
     * false will be returned. 
     * 
     * @param type $name 
     * @return array
     */
    protected function getSite( $name )
    {
        if ( isset( $this->sites[$name] ) ) {
            return $this->sites;
        }

        return false;
    }

    /**
     * Sets options for SMVPE Object
     * 
     * @param array $options 
     * @return SMVPE Object
     */
    public function setOptions( $options = array() )
    {
        if ( empty( $this->options ) ) {
            $defaults = array(
                'width'     => '860',
                'height'    => '480',                
                'container' => '<div class="video">%1$s</div>',
                'params'    => null
            );
        } else {
            $defaults = $this->options;
        }        

        $this->options = array_merge( $defaults, $options );

        return $this;
    }

    /**
     * Set or change a single option name => value
     * 
     * @param string $name 
     * @param mixed $value 
     * @return SMVPE Object
     */
    public function setOption( $name = '', $value = '' )
    {
        if ( trim( $name ) != '' ) {
            $this->options[$name] == $value;
        }

        return $this;
    }

    /**
     * Sets the source. Can be used if iterating through multiple sources.
     * 
     * @param string $source 
     * @param array $options 
     * @return SMVPE Object
     */
    public function setSource( $source = '', $options = array() )
    {
        $this->source   = $this->validateURL( $source );
        $this->site     = $this->getSourceProvider( $this->source );

        if ( ! empty( $options ) ) {
            $this->setOptions( $options );
        }

        return $this;
    }

    /**
     * Sets the source content by video ID and site slug name
     * 
     * @param string $id 
     * @param string $site 
     * @return SMVPE Object
     */
    public function setSourceByID( $id = '', $site = '' )
    {
        $site = (string) strtolower( trim( $site ) );

        if ( trim( $site ) != '' && isset( $this->sites[$site] ) && isset( $this->sites[$site]['embed'] ) ) {
            $source = $this->insertID( $id, $this->sites[$site]['embed'] );
            $this->setSource( $source );
        }

        return $this;
    }

    /**
     * Sets the parameters for the video embed
     * 
     * @param type $params 
     * @return type
     */
    public function setParameters( $params = '' )
    {
        $this->options['params'] = $params;
        return $this;
    }    

    /**
     * Evaluates and validates string as a URL
     * 
     * @param string $url 
     * @return string
     */
    public function validateURL( $url = '' )
    {
        if ( strpos( $url, '<' ) === false && strpos( $url, '>' ) === false ) {

            $valid_url = false;

            foreach ( array( 'http://', 'https://', '//' ) as $curl_dataeck ) {
                if ( strtolower( substr( $url, 0, strlen( $curl_dataeck ) ) ) == $curl_dataeck ) {
                    $valid_url = true;
                }
            }

            if ( $valid_url === false ) {
                $url = '//' . $url;
            }
        }

        return $url;
    }

    /**
     * Evaluates the source and returns a single value string specifying
     * the site of origin
     * 
     * @param type $source 
     * @return type
     */
    public function getSourceProvider( $source = null )
    {
        $source = ( $source === null ) ? $this->source : $source;
        $source = str_replace( '.', '', $source );
        
        foreach ( $this->sites as $site => $data ) {
            if ( strpos( $source, $site ) !== false ) {
                return $site;
            }
        }

        return false;
    }    

    /**
     * Evaluates and parses parameters and return as a query string
     * 
     * @param string/array $params 
     * @return string
     */
    protected function parseParameters( $params = null )
    {
        if ( $params === null ) {
            $params = $this->options['params'];
        }

        if ( is_string( $params ) ) {
            $params = '?' . ltrim( $params, '?' );
        }

        if ( is_array( $params ) && ! empty( $params ) ) {
            $params = '?' . http_build_query( $params );
        }

        return $params;
    }

    /**
     * Extracts video ID from source. $this->getSites() must contain
     * the anonymous function 'extract' in order to evaluate and parse
     * the source string.
     * 
     * @param string $source 
     * @param string $site 
     * @return string
     */
    public function extractID( $source = null, $site = null )
    {
        $source = ( $source === null ) ? $this->source : $source;
        $site   = ( $site === null ) ? $this->getSourceProvider( $source ) : $site;
        $parsed = parse_url( $source );

        if ( isset( $parsed['path'] ) ) {
            $parsed['path'] = trim( $parsed['path'], '/' );
        }

        if ( isset( $this->sites[$site]['extract'] ) ) {
            return $this->sites[$site]['extract']( $parsed );
        } else {
            return 0;
        }
    }

    /**
     * Inserts video ID from source by replacing {id} with actual extracted ID
     * 
     * @param string $subject 
     * @param string $source 
     * @param string $id 
     * @return string
     */
    protected function insertID( $id = '', $source = '' )
    {
        return str_replace( '{id}', $id, $source );
    }

    /**
     * Fetches and returns data associated with video
     * 
     * @param string $source 
     * @param string $site 
     * @return mixed
     */
    public function getData( $source = null, $site = null )
    {
        $source = ( $source === null ) ? $this->source : $source;
        $site   = ( $site === null ) ? $this->getSourceProvider( $source ) : $site;

        if ( isset( $this->sites[$site]['data'] ) && $this->sites[$site]['data'] != '' && $video_id = $this->extractID( $source ) ) {

            $data_url   = $this->insertID( $video_id, $this->sites[$site]['data'] );
            $curl_data  = curl_init( $data_url );

            curl_setopt( $curl_data, CURLOPT_URL, $data_url );
            curl_setopt( $curl_data, CURLOPT_RETURNTRANSFER, true );
            curl_setopt( $curl_data, CURLOPT_FOLLOWLOCATION, true );
            curl_setopt( $curl_data, CURLOPT_SSL_VERIFYPEER, false );
            curl_setopt( $curl_data, CURLOPT_TIMEOUT, '3' );

            $data = curl_exec( $curl_data );
            curl_close( $curl_data );            

            return $data;
        }

        return '';
    }

    /**
     * Gets the generated embed HTML code
     * 
     * @param string $source 
     * @param string $site
     * @return string
     */
    public function getEmbedCode( $source = null, $site = null )
    {
        $source = ( $source === null ) ? $this->source : $source;
        $site   = ( $site === null ) ? $this->getSourceProvider( $source ) : $site;
        $output = '';

        if ( isset( $this->sites[$site] ) && $video_id = $this->extractID( $source ) ) {

            $embed_url  = $this->insertID( $video_id, $this->sites[$site]['embed'] );
            $output     = '<iframe src="' . $embed_url . $this->parseParameters() . '" width="' . $this->options['width'] . '" height="' . $this->options['height'] . '" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>';
            $output     = sprintf( $this->options['container'], $output );
        }

        return $output;
    }

    /**
     * Outputs the generated embed code
     * 
     * @param string $source 
     * @param string $site 
     * @return string
     */
    public function embed( $source = null, $site = null )
    {
        $source = ( $source === null ) ? $this->source : $source;
        $site   = ( $site === null ) ? $this->getSourceProvider( $source ) : $site;

        echo $this->getEmbedCode( $source, $site );
    }
}

?>