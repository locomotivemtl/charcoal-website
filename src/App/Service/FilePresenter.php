<?php

namespace App\Service;

use Exception;

// From 'psr/log'
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

// From 'slim/slim'
use Slim\Http\Uri;

// From 'charcoal-core'
use Charcoal\Model\ModelInterface;

// From 'charcoal-property'
use Charcoal\Property\PropertyInterface;

// From 'charcoal-translator'
use Charcoal\Translator\Translation;

/**
 * The App File Presenter Service.
 */
class FilePresenter implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * @param array $data
     */
    public function __construct($data)
    {
        $this->setBaseUrl($data['baseUrl']);
        $this->setLogger($data['logger']);
    }

    /**
     * Set the application's fully qualified base URL to the public web directory.
     *
     * @param  UriInterface|string $uri The base URI to the application's web directory.
     * @return self
     */
    public function setBaseUrl($uri)
    {
        if (is_string($uri)) {
            $this->baseUrl = Uri::createFromString($uri);
        } else {
            $this->baseUrl = $uri;
        }
        return $this;
    }

    /**
     * Retrieve the application's fully qualified base URL to the public web directory.
     *
     * @return UriInterface|null The base URI to the application's web directory.
     */
    public function baseUrl()
    {
        return $this->baseUrl;
    }

    /**
     * Retrieve the full path for a given file.
     *
     * @param  string|null $path
     * @return string|null
     */
    public function getFileUrl($path)
    {
        if (!empty($path)) {
            return sprintf('%s/%s', rtrim($this->baseUrl(), '/'), $path);
        }

        return null;
    }

    /**
     * Format the image for the given property value and retrieve the full path.
     *
     * @param  string|string[]|Translation $value     The image(s) to format.
     * @param  PropertyInterface|string    $property  The property related to the $value.
     * @param  ModelInterface              $object    The object related to the $property.
     * @param  string|null                 $size      The desired size.
     * @return string|null
     */
    public function getFormattedFileUrl($value, $property, ModelInterface $object, $size = null)
    {
        $path = $this->formatImageFor($value, $property, $object, $size);

        return $this->getFileUrl($path);
    }

    /**
     * Format the image for the given property.
     *
     * @param  PropertyInterface|string $property The property related to the image(s).
     * @param  ModelInterface|null      $object   The object related to the $prop.
     * @param  string|null              $size     The desired size.
     * @return string|null
     */
    public function formatImageFrom($property, ModelInterface $object, $size = null)
    {
        if (!$property instanceof PropertyInterface) {
            $property = $object->property($property);
        }

        $value = $object->propertyValue($property->ident());
        if (empty($value)) {
            return $value;
        }

        return $this->formatImage($value, $property, $object, $size);
    }

    /**
     * Format the image for the given property value.
     *
     * @param  string|string[]|Translation $value     The image(s) to format.
     * @param  PropertyInterface|string    $property  The property related to the $value.
     * @param  ModelInterface              $object    The object related to the $property.
     * @param  string|null                 $size      The desired size.
     * @return string|null
     */
    public function formatImageFor($value, $property, ModelInterface $object, $size = null)
    {
        if (empty($value)) {
            return $value;
        }

        if (!$property instanceof PropertyInterface) {
            $property = $object->property($property);
        }

        return $this->formatImage($value, $property, $object, $size);
    }

    /**
     * Format the image.
     *
     * @param  string|string[]|Translation $value    The image(s) to format.
     * @param  PropertyInterface           $property The property related to the $value.
     * @param  ModelInterface              $object   The object related to the $property.
     * @param  string|null                 $size     The desired size.
     * @return string|null
     */
    public function formatImage($value, PropertyInterface $property, ModelInterface $object, $size = null)
    {
        try {
            if (empty($value)) {
                return $value;
            }

            // SVG images don't need intermediary sizes
            if (!is_array($value) && pathinfo($value, PATHINFO_EXTENSION) === 'svg') {
                return $value;
            }

            $property->setVal($value);
            $size = $this->resolveImageSize($size, $object->metadata()->property($property->ident()));
            if (isset($size['pattern'])) {
                if ($value instanceof Translation) {
                    foreach ($value->data() as $lang => $trans) {
                        $value[$lang] = $this->resolveImageFormat($trans, $size['pattern'], $property);
                    }
                } elseif (is_array($value)) {
                    foreach ($value as $k => $v) {
                        $value[$k] = $this->resolveImageFormat($value[$k], $size['pattern'], $property);
                    }
                } else {
                    $value = $this->resolveImageFormat($value, $size['pattern'], $property);
                }
            }
        } catch (Exception $error) {
            $this->logger->warning(sprintf(
                'Format Image: \'%s\' with message "%s"',
                get_class($error),
                $error->getMessage()
            ));
        }

        return $value;
    }

    /**
     * Resolve the size of an image property.
     *
     * @param  string|null $size             The desired size.
     * @param  array       $propertyMetadata The property metadata to parse.
     * @return array|null Returns the configset for the default image format.
     */
    private function resolveImageSize($size, array $propertyMetadata)
    {
        if ($size === null) {
            $size = isset($propertyMetadata['default_size']) ? $propertyMetadata['default_size'] : 'default';
        }

        if (isset($propertyMetadata['sizes'][$size])) {
            return $propertyMetadata['sizes'][$size];
        } else {
            return null;
        }
    }

    /**
     * Resolve the format of an image.
     *
     * @param  string            $file     The file being renamed.
     * @param  string            $pattern  The pattern replacing $from.
     * @param  PropertyInterface $property The related property.
     * @return string Returns the renamed file if it exists. Otherwise, returns the given $file.
     */
    private function resolveImageFormat($file, $pattern, PropertyInterface $property)
    {
        $value = $file;

        if (parse_url($value, PHP_URL_SCHEME)) {
            $info  = pathinfo($value);
            $value = str_replace($info['dirname'], rtrim($property->getUploadPath(), '/'), $value);
        }

        if (!pathinfo($value, PATHINFO_EXTENSION)) {
            return $file;
        }

        $renamed = $property->renderFileRenamePattern($value, $pattern);
        if ($property->fileExists($renamed)) {
            return $renamed;
        }

        return $file;
    }
}
