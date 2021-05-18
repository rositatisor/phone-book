<?php

namespace App\Tests\Entity;

use App\Entity\Contact;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ContactTest extends KernelTestCase
{
    public function getEntity(): Contact
    {
        return (new Contact)
            ->setName('Li')
            ->setPhone('37060000000')
            ->setUser(new User)
            ->setFavourite('no');
    }

    public function assertHasErrors(Contact $contact, int $number = 0)
    {
        self::bootKernel();
        $errors = self::$container->get('validator')->validate($contact);
        $messages = [];
        foreach ($errors as $error) {
            $messages[] = $error->getPropertyPath().' => '.$error->getMessage();
        }
        $this->assertCount($number, $errors, implode(', ', $messages));
    }

    public function testValidEntity () 
    {
        $this->assertHasErrors($this->getEntity(), 0);
    }
    
    public function testInvalidNameEntity () 
    {
        $this->assertHasErrors($this->getEntity()->setName('LlanfairpwllgwyngyllgogerychwyrndrobwllllantysiliogogogochABCDEFG'), 1);
        $this->assertHasErrors($this->getEntity()->setName('11'), 2);
        $this->assertHasErrors($this->getEntity()->setName('L1'), 1);
        $this->assertHasErrors($this->getEntity()->setName('1'), 3);
        $this->assertHasErrors($this->getEntity()->setName('/'), 2);
        $this->assertHasErrors($this->getEntity()->setName('L'), 1);
    }

    public function testInvalidBlankNameEntity () 
    {
        $this->assertHasErrors($this->getEntity()->setName(''), 2);
    }

    public function testInvalidPhoneEntity () 
    {
        $this->assertHasErrors($this->getEntity()->setPhone('+370600000'), 1);
        $this->assertHasErrors($this->getEntity()->setPhone('+3706a0000'), 1);
        $this->assertHasErrors($this->getEntity()->setPhone('86a000000'), 1);
        $this->assertHasErrors($this->getEntity()->setPhone('86000000'), 1);
        $this->assertHasErrors($this->getEntity()->setPhone('8600000'), 1);
        $this->assertHasErrors($this->getEntity()->setPhone('860000'), 1);
        $this->assertHasErrors($this->getEntity()->setPhone('86000'), 1);
        $this->assertHasErrors($this->getEntity()->setPhone('8600'), 1);
        $this->assertHasErrors($this->getEntity()->setPhone('860'), 1);
        $this->assertHasErrors($this->getEntity()->setPhone('86'), 1);
        $this->assertHasErrors($this->getEntity()->setPhone('8'), 1);
        $this->assertHasErrors($this->getEntity()->setPhone('a'), 2);
        $this->assertHasErrors($this->getEntity()->setPhone('+'), 2);
        $this->assertHasErrors($this->getEntity()->setPhone('-'), 2);
        $this->assertHasErrors($this->getEntity()->setPhone('/'), 2);
    }

    public function testInvalidBlankPhoneEntity () 
    {
        $this->assertHasErrors($this->getEntity()->setPhone(''), 2);
    }
}