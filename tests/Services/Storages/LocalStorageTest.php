<?php
namespace Rocketeer\Services\Storages;

use Rocketeer\TestCases\RocketeerTestCase;

class LocalStorageTest extends RocketeerTestCase
{
	////////////////////////////////////////////////////////////////////
	//////////////////////////////// TESTS /////////////////////////////
	////////////////////////////////////////////////////////////////////

	public function testCanCreateDeploymentsFileAnywhere()
	{
		$this->app['path.storage'] = null;
		$this->app->offsetUnset('path.storage');

		new LocalStorage($this->app);

		$storage = $this->rocketeer->getRocketeerConfigFolder();
		$exists  = file_exists($storage);
		$this->files->deleteDirectory($storage);
		$this->assertTrue($exists);
	}

	public function testCanGetLineEndings()
	{
		$this->localStorage->destroy();

		$this->assertEquals(PHP_EOL, $this->localStorage->getLineEndings());
	}

	public function testCanGetSeparators()
	{
		$this->localStorage->destroy();

		$this->assertEquals(DIRECTORY_SEPARATOR, $this->localStorage->getSeparator());
	}

	public function testCanComputeHashAccordingToContentsOfFiles()
	{
		$this->mock('files', 'Filesystem', function ($mock) {
			return $mock
				->shouldReceive('put')->once()
				->shouldReceive('exists')->twice()->andReturn(false)
				->shouldReceive('glob')->once()->andReturn(array('foo', 'bar'))
				->shouldReceive('getRequire')->once()->with('foo')->andReturn(array('foo'))
				->shouldReceive('getRequire')->once()->with('bar')->andReturn(array('bar'));
		});

		$hash = $this->localStorage->getHash();

		$this->assertEquals(md5('["foo"]["bar"]'), $hash);
	}

	public function testCanCheckIfComposerIsNeeded()
	{
		$this->usesComposer(true);
		$this->assertTrue($this->localStorage->usesComposer());

		$this->usesComposer(false);
		$this->assertFalse($this->localStorage->usesComposer());
	}
}