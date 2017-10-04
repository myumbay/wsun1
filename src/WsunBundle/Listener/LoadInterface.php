<?php

namespace AppBundle\Imagine\Binary\Loader;

use Liip\ImagineBundle\Model\FileBinary;
use Liip\ImagineBundle\Binary\Loader\LoaderInterface;
use Symfony\Component\HttpFoundation\File\MimeType\ExtensionGuesserInterface;
use Symfony\Component\HttpFoundation\File\MimeType\MimeTypeGuesserInterface;

class MyCustomDataLoader implements LoaderInterface
{
    /**
     * @var MimeTypeGuesserInterface
     */
    protected $mimeTypeGuesser;

    /**
     * @var ExtensionGuesserInterface
     */
    protected $extensionGuesser;

    /**
     * @var string
     */
    protected $dataRoot;

    /**
     * @param MimeTypeGuesserInterface  $mimeGuesser
     * @param ExtensionGuesserInterface $extensionGuesser
     * @param string                    $dataRoots
     */
    public function __construct(MimeTypeGuesserInterface $mimeGuesser, ExtensionGuesserInterface $extensionGuesser, $dataRoot)
    {
        $this->mimeTypeGuesser = $mimeGuesser;
        $this->extensionGuesser = $extensionGuesser;
        $this->dataRoot = $dataRoot;
    }

    /**
     * {@inheritdoc}
     */
    public function find($path)
    {
        $path = $this->dataRoot.DIRECTORY_SEPARATOR.$path;   
        /* Perform any security checks you'd like on $path */
        $mime = $this->mimeTypeGuesser->guess($path);

        return new FileBinary($path, $mime, $this->extensionGuesser->guess($mime));
    }
}