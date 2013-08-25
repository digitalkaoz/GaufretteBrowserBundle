<?php
namespace rs\GaufretteBrowserBundle\Tests\Entity;

use rs\GaufretteBrowserBundle\Entity\FileRepository;
use Gaufrette\File;
/**
 * @covers rs\GaufretteBrowserBundle\Entity\FileRepository<extended>
 */
class FileRepositoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FileRepository
     */
    private $repo;

    const CLS = 'rs\GaufretteBrowserBundle\Entity\File';

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
        $this->repo = new FileRepository($this->eventDispatcher, $this->fs, self::CLS);
    }

    public function testGetClassName()
    {
        $this->assertEquals(self::CLS, $this->repo->getClassName());
    }

    public function testFindAll()
    {
        $this->fs->expects($this->atLeastOnce())->method('listKeys')->will($this->returnValue(array('keys'=>array('foo.png','bar.jpg'))));
        $this->fs->expects($this->atLeastOnce())->method('get')->will($this->returnValue($this->gaufretteFile));

        $result = $this->repo->findAll();

        $this->assertInstanceOf('Doctrine\Common\Collections\ArrayCollection', $result);
        $this->assertCount(2, $result->getIterator());
    }

    public function testFind()
    {
        $key = 'foo.png';

        $this->fs->expects($this->atLeastOnce())->method('has')->with($key)->will($this->returnValue(true));
        $this->fs->expects($this->atLeastOnce())->method('get')->with($key)->will($this->returnValue($this->gaufretteFile));
        $this->adapter->expects($this->atLeastOnce())->method('isDirectory')->with($key)->will($this->returnValue(false));

        $result = $this->repo->find($key);

        $this->assertInstanceOf(self::CLS, $result);
    }

    public function testFindOneBy()
    {
        $prefix = '/foo/';
        $suffix = '/\.png/';

        $fileA = new File('/foo/foo.png', $this->fs);
        $fileB = new File('/foo/bar.jpg', $this->fs);

        $this->fs->expects($this->atLeastOnce())->method('listKeys')->with($prefix)->will($this->returnValue(array('keys'=>array('bar.jpg','foo.png'))));
        $this->fs->expects($this->at(1))->method('get')->with('bar.jpg')->will($this->returnValue($fileB));
        $this->fs->expects($this->at(2))->method('get')->with('foo.png')->will($this->returnValue($fileA));

        $result = $this->repo->findOneBy(array('prefix'=>$prefix,'suffix'=>$suffix));

        $this->assertInstanceOf(self::CLS, $result);

        $this->assertEquals('/foo/foo.png', $result->getKey());
    }

    public function testFindBy()
    {
        $prefix = '/foo/';
        $suffix = '/\.(png|jpg)/';

        $fileA = new File('/foo/foo.png', $this->fs);
        $fileB = new File('/foo/bar.jpg', $this->fs);
        $fileC = new File('/bar/bar.png', $this->fs);

        $this->fs->expects($this->atLeastOnce())->method('listKeys')->with($prefix)->will($this->returnValue(array('keys'=>array('bar.jpg','foo.png','bar.png'))));
        $this->fs->expects($this->at(1))->method('get')->with('bar.jpg')->will($this->returnValue($fileB));
        $this->fs->expects($this->at(2))->method('get')->with('foo.png')->will($this->returnValue($fileA));
        $this->fs->expects($this->at(3))->method('get')->with('bar.png')->will($this->returnValue($fileC));

        $result = $this->repo->findBy(array('prefix'=>$prefix,'suffix'=>$suffix), array('name' => 'ASC'));

        $this->assertInstanceOf('Doctrine\Common\Collections\ArrayCollection', $result);
        $this->assertCount(2, $result->getIterator());
    }
}
