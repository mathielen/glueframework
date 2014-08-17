<?php
namespace Infrastructure\Email;

class SwiftMailMessageFactory
{

    /**
     * @var \Twig_Environment
     */
    private $engine;

    public function __construct(\Twig_Environment $engine)
    {
        $this->engine = $engine;
    }

    /**
     * @return \Swift_Message
     */
    public function createMessage($template, array $data)
    {
        $tpl = $this->engine->loadTemplate($template);

        $subject = $tpl->renderBlock('subject', $data);
        $body = $tpl->renderBlock('body', $data);

        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setBody($body);

        return $message;
    }

}
