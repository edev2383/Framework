<?php

namespace Edev\Resource\Email\Routing;

use Edev\Database\Manager\Manager;
use Edev\Database\Manager\MetaConn;
use Edev\Model\Employee;
use Edev\Model\MetaClient;
use Edev\Model\Model;
use Edev\Resource\Display\Template\Templater;
use Edev\Resource\Email;
use Edev\Resource\EmailTemplate;
use Edev\System\Helpers\Arr;

/**
 *
 */
class TicketReply
{

    private $emailParser;
    private $domain = '@zerodock.com';

    private $ticketAuthor;
    private $replyTo;
    private $ticket;
    private $client;

    private $replyAuthor;
    private $subscriptions;

    /**
     *
     * @param \Edev\Resource\Email\Parser
     * */
    public function __construct($emailParser)
    {

        $this->emailParser = $emailParser;

        $this->_init();
        // parser contains to, from, subject, message props
    }

    private function _init()
    {
        // mail('jeff@jasoncases.com', 'Ticket REPLY captured', print_r($this->emailParser, true));

        $this->_registerMetaConnection();
        $this->_parseTo($this->emailParser->to);
        $this->_getReplyAuthor();
        $this->_formatReplyEmail();
        $this->_storeReplyComponents();
    }

    private function _getReplyAuthor()
    {
        $this->replyAuthor = Employee::where(
            'email',
            $this->emailParser->from
        )
            ->get('id as employee_id', 'first_name as name', 'email');
    }

    private function _storeReplyComponents()
    {
        $this->_storeReplyAuthor();
        $this->_storeReply();
        $this->_subscribeReplyAuthor();
    }

    private function _storeReply()
    {
        $author = $this->replyAuthor['employee_id'];
        $ticket_id = $this->ticket['ticket_id'];
        $reply = $this->emailParser->message;
        return $this->_mockModel('mod_ticket_replies')
            ->save(compact('author', 'ticket_id', 'reply'));
    }

    private function _formatReplyEmail()
    {
        $this->_setAuthorEmail();
        $this->_setReplyTo();
        $this->_getSubscriptions();
        $this->_setEmailComponents();
    }

    private function _setEmailComponents()
    {

        $send = Email::to($this->ticketAuthor['email'])
            ->from($this->replyTo, "Edev Ticket System")
            ->message($this->_formatReplyEmailSubject())
            ->addCC(array_column($this->subscriptions, 'email'))
            ->subject($this->emailParser->subject)
            ->compose();
        if (!$send) {
            Log::error('Error sending Ticket Reply email', $this);
            mail('jeff@jasoncases.com', 'Error sending Ticket ReplyEmail', json_encode($this));
        }
    }

    private function _formatReplyEmailSubject()
    {
        $replyAuthor = $this->replyAuthor['email'];
        $ticket_id = $this->ticket['ticket_id'];
        $reply = $this->emailParser->message;
        return Templater::parse(EmailTemplate::load('ticket/reply'), compact('replyAuthor', 'ticket_id', 'reply'));
    }

    private function _getSubscriptions()
    {
        $subs = $this->_mockModel('v_mod_ticket_subscriptions');
        $this->subscriptions = $subs->where('ticket_id', $this->ticket['ticket_id'])
            ->round()
            ->get('name', 'email', 'employee_id');
    }

    private function _storeReplyAuthor()
    {
        $users = $this->_mockModel('mod_ticket_users');
        if (!$users->where('email', $this->emailParser->from)->get()) {
            return $users->save($this->replyAuthor);
        }
        return true;
    }

    private function _subscribeReplyAuthor()
    {
        $subs = $this->_mockModel('mod_ticket_subscriptions');
        $subscriber_id = $this->replyAuthor['employee_id'];
        $ticket_id = $this->ticket['ticket_id'];
        $exists = $subs->where('subscriber_id', $subscriber_id)
            ->andWhere('ticket_id', $ticket_id)
            ->get();
        if (!$exists) {
            return $subs->save(compact('subscriber_id', 'ticket_id'));
        }
        return true;
    }

    private function _setAuthorEmail()
    {
        $this->ticketAuthor = $this->_mockModel('mod_ticket_users')
            ->where('employee_id', $this->ticket['author'])
            ->get();
    }

    private function _setReplyTo()
    {
        $this->replyTo = $this->emailParser->to;
    }

    /**
     * Undocumented function
     *
     * @param string $to
     * @return void
     */
    private function _parseTo(string $to)
    {
        [
            $prefix,
            $ticket,
            $hash,
        ] = explode('-', str_replace($this->domain, '', $to));
        $this->client = $this->_getClientByPrefix($prefix);
        $this->_registerClientConnection();
        $this->ticket = $this->_getTicketByHash($hash);
    }

    private function _getTicketByHash(string $hash)
    {
        return $this->_mockModel('mod_tickets')
            ->where('hash', $hash)
            ->get('author', 'id as ticket_id');
    }

    private function _getClientByPrefix(string $prefix)
    {
        return MetaClient::where('client_system_prefix', $prefix)->get(
            'client_system_prefix',
            'uid',
        );
    }

    private function _registerMetaConnection()
    {
        new MetaConn();
    }

    private function _registerClientConnection()
    {
        $conn = new Manager();
        $conn->forceConnect($this->client['client_system_prefix']);
    }

    private function _mockModel($table)
    {
        $mockModel = new Model();
        $mockModel->setTable($table);
        $builder = $mockModel->newModelBuilder();
        $builder->setModel($mockModel);
        return $builder;
    }
}
