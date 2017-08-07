<?php

declare(strict_types=1);

namespace GraphQL\Doctrine;

use Doctrine\Common\Annotations\Reader;
use ReflectionMethod;
use ReflectionParameter;

/**
 * A basic doc block reader to extract
 */
class DocBlockReader
{
    /**
     * @var string
     */
    private $comment;

    public function __construct(ReflectionMethod $method)
    {
        $this->comment = $method->getDocComment() ?: '';
    }

    /**
     * Get the description of a method from the doc block
     * @param ReflectionMethod $method
     * @return string|null
     */
    public function getMethodDescription(): ?string
    {
        // Remove the comment markers
        $description = preg_replace('~^\s*(/\*\*|\* ?|\*/)~m', '', $this->comment);

        // Keep everything before the first annotation
        $description = trim(explode('@', $description)[0]);

        // Drop common "Get" or "Return" in front of comment
        $description = ucfirst(preg_replace('~^(get|return)s? ~i', '', $description));

        return $description ?: null;
    }

    /**
     * Get the parameter description
     * @param ReflectionParameter $param
     * @return string|null
     */
    public function getParameterDescription(ReflectionParameter $param): ?string
    {
        $name = preg_quote($param->getName());

        if (preg_match('~@param\h+\H+\h+\$' . $name . '\h+(.*)~', $this->comment, $m)) {
            return ucfirst(trim($m[1]));
        }

        return null;
    }

    /**
     * Get the parameter description
     * @param ReflectionParameter $param
     * @return string|null
     */
    public function getParameterType(ReflectionParameter $param): ?string
    {
        $name = preg_quote($param->getName());

        if (preg_match('~@param\h+(\H+)\h+\$' . $name . '(\h|$)~', $this->comment, $m)) {
            return trim($m[1]);
        }

        return null;
    }

    public function getReturnType(): ?string
    {
        if (preg_match('~@return\h+(\H+)(\h|$)~', $this->comment, $m)) {
            return trim($m[1]);
        }

        return null;
    }
}