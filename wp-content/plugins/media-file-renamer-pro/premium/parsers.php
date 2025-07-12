<?php

class MeowPro_MFRH_Parsers {

	private $core = null;

	public function __construct( $core )
	{

		$this->core = $core;
		$parsers = $this->core->get_option( 'parsers', [] );

		$enabled_parsers = [];
        foreach ( $parsers as $key => $parser ) {
            $enabled_parsers[ $key ] = $parser['exists'] && $parser['enabled'];
        }


		// Elementor
		if ( $enabled_parsers['elementor'] )
		{
			require_once( 'parsers/elementor.php' );
		}

		// Oxygen
		if ( $enabled_parsers['oxygen'] )
		{
			require_once( 'parsers/oxygen_builder.php' );
		}

		// Beaver Builder
		if ( $enabled_parsers['beaver_builder'] )
		{
			require_once( 'parsers/beaver_builder.php' );
		}

		// WPML
		if ( $enabled_parsers['wpml'] )
		{
			require_once( 'parsers/wpml.php' );
		}

	
	}

}
