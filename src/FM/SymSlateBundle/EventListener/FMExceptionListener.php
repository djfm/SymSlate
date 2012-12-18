<?

namespace FM\SymSlateBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class FMExceptionListener
{
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        
    }
}