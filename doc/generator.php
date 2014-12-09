<?php

namespace Timely\Ai1ec\Skeleton\Documentation;

$base_dir     = dirname( __DIR__ );

$documentable = new \RegexIterator(
	new Ai1ecsaBuilderFilter(
        new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(
                $base_dir,
                \FilesystemIterator::SKIP_DOTS
            )
        ),
		$base_dir
	),
	'/^.+\.php$/i',
	\RegexIterator::GET_MATCH
);

foreach ( $documentable as $entry ) {
	$php_file = $entry[0];
	$md_file  = \dirname( $php_file ) . DIRECTORY_SEPARATOR .
		\basename( $php_file, '.php' ) . '.md';
	\file_put_contents(
		$md_file,
		Generator::parse( $php_file )
	);
}

class Generator {

	protected $file;

	static public function parse( $file ) {
		$parser = new self( $file );
		return $parser->extract_md();
	}

	public function __construct( $file ) {
		if ( ! is_file( $file ) || ! is_readable( $file ) ) {
			throw new InvalidArgumentException(
				'File \'' . $file . '\' is unreadable'
			);
		}
		$this->file = $file;
	}

	public function extract_md() {
		$tokens = token_get_all( file_get_contents( $this->file ) );

	}

}

class Ai1ecsaBuilderFilter extends \FilterIterator {

	protected $base_path;

	public function __construct( \Iterator $rit, $path ) {
        $this->base_path   = $path;
        $this->base_substr = strlen( $path );
		parent::__construct( $rit );
    }

	public function accept() {
		if (
            0 === strpos( $this->current()->getPathname(), $this->base_path . '/doc' ) ||
            0 === strpos( $this->current()->getPathname(), $this->base_path . '/.git' )
        ) {
			return false;
		}
		return true;
	}

}