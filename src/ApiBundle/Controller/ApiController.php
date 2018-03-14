<?php

namespace ApiBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use ApiBundle\Entity\Posts;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;

class ApiController extends FOSRestController
{
    /**
     * @var array Request result
     */
    public $result = [
        'error' => false,
        'data' => [],
        'messages' => []
    ];

    /**
     * @Rest\Get("/api/posts")
     */
    public function getAction()
    {
        $this->result['data'] = $this->getDoctrine()->getRepository('ApiBundle:Posts')->findAll();
        if (!$this->result['data']) {
            $this->setError('There are no posts exist');
        }
        return $this->result;
    }

    /**
     * @Rest\Post("/api/post")
     */
    public function postAction(Request $request)
    {
        $post = new Posts;
        $title = strip_tags($request->get('title'));
        $body = strip_tags($request->get('body'));
        //Use Validation component
        $validator = Validation::createValidator();
        //Title validation
        $errors = $validator->validate($title, [new NotBlank(), new Type('string'), new Length(['min' => 3, 'max' => 255])]);
        if (count($errors) > 0) {
            foreach ($errors as $error) {
                $this->setError('(Title):' . $error->getMessage());
            }
        }
        //Body Validation
        $errors = $validator->validate($body, [new Type('string')]);
        if (count($errors) > 0) {
            foreach ($errors as $error) {
                $this->setError('(Body):' . $error->getMessage());
            }
        }
        if (!$this->result['error']) {
            $post->setTitle($title);
            $post->setBody($body);
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($post);
            $manager->flush();
            $this->result['data'][] = $post;
        }
        return $this->result;
    }

    /**
     * @param string $message Error message
     */
    protected function setError(string $message)
    {
        if (!$this->result['error']) {
            $this->result['error'] = true;
        }
        $this->result['messages'][] = $message;
    }
}