<?php

class BlacklistsTest extends PHPUnit_Framework_TestCase{
    public function test_checkSrtingFormat(){
        $this->assertTrue(Blacklists::checkSrtingFormat("p1, s111, p2, s222"));
    }
    
    public function test_checkAdvertisers(){
        new Blacklists();
        $this->assertFalse(Blacklists::checkAdvertisers(500));
    }
    
    public function test_checkList(){
        new Blacklists();
        $this->assertArraySubset([500, 600], Blacklists::checkList([500, 600], 'sites'));
        $this->assertArraySubset([500, 600], Blacklists::checkList([500, 600], 'publishers'));
    }
    
    public function test_getBlackList(){
        new Blacklists();
        $this->assertEmpty(Blacklists::getBlackList(500));
    }
    
    public function test_save(){
        $blacklistObj = new Blacklists();
        $this->assertTrue($blacklistObj->save('p1, s111, p2, s222, s123, s125, p3', 1));
    }
}

