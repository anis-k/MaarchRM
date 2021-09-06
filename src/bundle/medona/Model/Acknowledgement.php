<?php
namespace bundle\medona\Model;
/**
 * The acknowledgement message
 *
 * @package Medona
 * @author  Cyril VAZQUEZ (Maarch) <cyril.vazquez@maarch.org>
 *
 */
class Acknowledgement extends AbstractBusinessMessage
{

    /**
     * @var medona/Identifier
     * @xpath medona:AcknowledgementIdentifier
     */
    public $acknowledgementIdentifier;

    /**
     * @var medona/Identifier
     * @xpath medona:MessageReceivedIdentifier
     */
    public $messageReceivedIdentifier;

    /**
     * @var medona/Organization
     * @xpath medona:Receiver
     */
    public $receiver;

    /**
     * @var medona/Organization
     * @xpath medona:Sender
     */
    public $sender;
}
