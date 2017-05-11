<?php

/**
 * Script that checks whether archive version of files in database are in sync with DFS
 *
 * @see SUS-1511
 *
 * @author Macbre
 * @ingroup Maintenance
 */

require_once( __DIR__ . '/../Maintenance.php' );

/**
 * Maintenance script class
 */
class CheckArchiveFiles extends Maintenance {

	/**
	 * Set script options
	 */
	public function __construct() {
		parent::__construct();
		$this->addOption( 'do-the-stuff', 'Really perform cleanup operations' );
		$this->mDescription = 'This script checks whether archive version of files in database are in sync with DFS';
	}

	public function execute() {
		global $wgDBname;

		// get archived versions of images
		$dbr = $this->getDB( DB_SLAVE );
		$rows = $dbr->select(
			'oldimage',
			OldLocalFile::selectFields(),
			[
				// skip deleted entries - these files are stored in a different directory on DFS
				'oi_deleted' => 0,
			],
			__METHOD__
		);

		// skip early if there are no files to process
		$filesCount = $dbr->affectedRows();
		if ($filesCount === 0) {
			$this->output( sprintf( "%s - no archived files found\n", $wgDBname ) );
			return;
		}

		$this->output( sprintf( "%s - files to check: %d\n", $wgDBname, $filesCount ) );

		$filesToRemove = 0;
		$localRepo = RepoGroup::singleton()->getLocalRepo();

		foreach( $rows as $row ) {
			$file = OldLocalFile::newFromRow( $row, $localRepo );

			if ( !$file->exists() ) {
				$filesToRemove++;
				$this->output( sprintf( "%s - archived version of a file is missing: %s (%s) / year %d\n", $wgDBname, $file->getName(), $file->getTimestamp(), substr($file->getTimestamp(), 0, 4) ) );

				if ( $this->hasOption( 'do-the-stuff' ) ) {
					// TODO: delete an entry from oldimage table
				}
			}
		}

		$this->output( sprintf( "%s - archived files: %d, to be removed: %d (%.2f%%)\n", $wgDBname, $filesCount, $filesToRemove, 100 * $filesToRemove / $filesCount ) );
	}
}

$maintClass = CheckArchiveFiles::class;
require_once( RUN_MAINTENANCE_IF_MAIN );
