<?php

namespace App\Model;

// From App
use App\Model\Common\AbstractContent;

/**
 * Model: Team Member
 */
class CaseStudy extends AbstractContent
{
    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $thumbnail;

    /**
     * @var string
     */
    private $url;

    /**
     * @var mixed
     */
    private $tags;


    /**
     * @param array $data
     */
    public function __construct(array $data = null)
    {
        parent::__construct($data);

        if (is_callable([ $this, 'defaultData' ])) {
            $this->setData($this->defaultData());
        }
    }



    // Properties
    // -------------------------------------------------------------------------

    /**
     * @param  mixed $title
     * @return void
     */
    public function setTitle($title)
    {
        $this->title = $this->property('title')->parseVal($title);
    }

    /**
     * @return string|null
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param  string $path
     * @return void
     */
    public function setThumbnail($path)
    {
        $this->thumbnail = $this->property('thumbnail')->parseVal($path);
    }

    /**
     * @return string|null
     */
    public function getThumbnail()
    {
        return $this->thumbnail;
    }

    /**
     * @param  mixed $Url
     * @return void
     */
    public function setUrl($url)
    {
        $this->url = $this->translator()->translation($url);
        return $this;
    }

    /**
     * @return string|null
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param mixed $tags
     * @return void
     */
    public function setTags($tags)
    {
        $this->tags = $this->property('tags')->parseVal($tags);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTags()
    {
        return $this->tags;
    }
}
