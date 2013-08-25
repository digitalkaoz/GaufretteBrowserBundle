<?php
namespace rs\GaufretteBrowserBundle\Tests\Entity;

use Gaufrette\File;
use rs\GaufretteBrowserBundle\Entity\DirectoryRepository;

/**
 * @covers rs\GaufretteBrowserBundle\Entity\DirectoryRepository<extended>
 */
class DirectoryRepositoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DirectoryRepository
     */
    private $repo;

    const CLS = 'rs\GaufretteBrowserBundle\Entity\Directory';

    public function setUp()
    {
        $this->adapter = $this->getMock('Gaufrette\Adapter');
        $this->eventDispatcher = $this->getMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');
        $this->eventDispatcher->expects($this->any())->method('dispatch')->will($this->returnArgument(1));
        $this->fs = $this->getMockBuilder('Gaufrette\Filesystem')
            ->disableOriginalConstructor()
            ->setMethods(array('has','get', 'getAdapter','listKeys'))
            ->getMock();

        $this->fs->expects($this->any())->method('getAdapter')->will($this->returnValue($this->adapter));
        $this->gaufretteFile = new File('foo', $this->fs);
        $this->repo = new DirectoryRepository($this->eventDispatcher, $this->fs, self::CLS);
    }

    public function testGetClassName()
    {
        $this->assertEquals(self::CLS, $this->repo->getClassName());
    }

    public function testFindAll()
    {
        $this->fs->expects($this->atLeastOnce())->method('listKeys')->will($this->returnValue(array('dirs'=>array('/foo','/bar'))));
        $this->fs->expects($this->atLeastOnce())->method('get')->will($this->returnValue($this->gaufretteFile));

        $result = $this->repo->findAll();

        $this->assertInstanceOf('Doctrine\Common\Collections\ArrayCollection', $result);
        $this->assertCount(2, $result->getIterator());
    }

    public function testFind()
    {
        $key = '/foo';

        $this->fs->expects($this->atLeastOnce())->method('has')->with($key)->will($this->returnValue(true));
        $this->fs->expects($this->atLeastOnce())->method('get')->with($key)->will($this->returnValue($this->gaufretteFile));
        $this->adapter->expects($this->atLeastOnce())->method('isDirectory')->with($key)->will($this->returnValue(true));

        $result = $this->repo->find($key);

        $this->assertInstanceOf(self::CLS, $result);
    }

    public function testFindOneBy()
    {
        $prefix = '/foo/';
        $suffix = '/foob/';

        $fileA = new File('/foo/foobar', $this->fs);
        $fileB = new File('/foo/bar', $this->fs);

        $this->fs->expects($this->atLeastOnce())->method('listKeys')->with($prefix)->will($this->returnValue(array('dirs'=>array('/foo/bar','/foo/foobar'))));
        $this->fs->expects($this->at(1))->method('get')->with('/foo/bar')->will($this->returnValue($fileB));
        $this->fs->expects($this->at(2))->method('get')->with('/foo/foobar')->will($this->returnValue($fileA));

        $result = $this->repo->findOneBy(array('prefix'=>$prefix,'suffix'=>$suffix));

        $this->assertInstanceOf(self::CLS, $result);

        $this->assertEquals('/foo/foobar', $result->getKey());
    }

    public function testFindBy()
    {
        $prefix = '/foo/';
        $suffix = '/bar/';

        $fileA = new File('/foo/foobar', $this->fs);
        $fileB = new File('/foo/bar', $this->fs);
        $fileC = new File('/bar/bar', $this->fs);

        $this->fs->expects($this->atLeastOnce())->method('listKeys')->with($prefix)->will($this->returnValue(array('dirs'=>array('/foo/bar','/foo/foobar','/bar/bar'))));
        $this->fs->expects($this->at(1))->method('get')->with('/foo/bar')->will($this->returnValue($fileB));
        $this->fs->expects($this->at(2))->method('get')->with('/foo/foobar')->will($this->returnValue($fileA));
        $this->fs->expects($this->at(3))->method('get')->with('/bar/bar')->will($this->returnValue($fileC));

        $result = $this->repo->findBy(array('prefix'=>$prefix,'suffix'=>$suffix), array('name' => 'ASC'));

        $this->assertInstanceOf('Doctrine\Common\Collections\ArrayCollection', $result);
        $this->assertCount(2, $result->getIterator());
    }

}
