<?php
/**
 * Test case that tests database user operations that normally are performed via the Admin 
 * actions
 * 
 */

class icingaUserOperations extends PHPUnit_Framework_TestCase {
	public static function setUpBeforeClass() {
		Doctrine_Manager::connection()->beginTransaction();
		$context = AgaviContext::getInstance();
		$context->getUser()->addCredential("appkit.admin");
		$context->getUser()->addCredential("appkit.admin.users");
		$context->getUser()->addCredential("appkit.admin.groups");
		$context->getUser()->addCredential("icinga.user");
		$context->getUser()->setAuthenticated(true);
	}
	
	protected $params = array(
		"id"=>"new",
		"user_id" => "new",
		"password_validate"=>"testpass",
		"password"=>"testpass",
		"user_name" => "TESTCASE",
		"user_lastname" => "TESTCASE",
		"user_firstname" => "TESTCASE",
		"user_email" => "test@testcase.local",
		"user_disabled" => 0
	);
	
	protected $alterParams = array(

		"password_validate"=>"newpass",
		"password"=>"newpass",
		"user_name" => "TESTCASE_ALTERED",
		"user_lastname" => "TESTCASE_ALTERED",
		"user_firstname" => "TESTCASE_ALTERED",
		"user_email" => "altered@testcase.local",
		"user_disabled" => 1
	);
	
	/**
	 * @dataProvider addUserParameter
	 */
	public function testUserAdd() {
		try {
			info("\tTesting admin 'Add user' action\n");
			$context = AgaviContext::getInstance();
			$arguments = new AgaviRequestDataHolder();
			$user = $this->params;
			foreach($user as $name=>$value) {
				$arguments->setParameter($name,$value);							
			}
			
			// insert user
			$container = $context->getController()->createExecutionContainer("AppKit","Admin.Users.Edit",$arguments,"simple","write");
			$this->assertType("AgaviExecutionContainer",$container,"Couldn't create add-user action");
			
			$result = $container->execute();
			$this->assertNotEquals($result->getHttpStatusCode(),"404","Action for adding users not found");				

			// Check if user is really added
			$result = Doctrine_Core::getTable("NsmUser")->findBy("user_name",$user["user_name"])->getFirst();		
			$this->assertType("NsmUser",$result,"No user found, something seemed to go wrong");
			return $result->get("user_id");
			success("\tCreated user exists!\n");
		} catch(Exception $e) {
			$this->fail("Adding a new user failed : ".$e->getMessage());
		}
		return true;

	}
	
	/**
	 * @depends testUserAdd
	 */
	public function testUserSelect($id) {
		try {
			info("\tTesting icinga-web action : List users\n");
			$context = AgaviContext::getInstance();
			$directDBList_all = Doctrine_Query::create()->select("n.*,pr.*,r.*")->from("NsmUser n")->leftJoin("n.NsmPrincipal pr")->leftJoin("n.NsmUserRole r")->execute()->toArray(true);
			$this->assertGreaterThan(0,count($directDBList_all),"A really strange error occured - suddenly couldn't fetch any users");
			
			$sortOrder = new AgaviRequestDataHolder();
			$sortOrder->setParameter("sort","user_id");
			$icingaListing_all = $context->getController()->createExecutionContainer("AppKit","DataProvider.UserProvider",$sortOrder,"json");
			$result = $icingaListing_all->execute();

			$this->assertNotEquals($result->getHttpStatusCode(),"404","Action for adding users not found");				
			
			$json = json_decode($result->getContent(),true);

			$this->assertNotNull($json,"Couldn't fetch user list via the icinga action");
			$this->assertTrue(isset($json["users"]),"Couldn't fetch user list via the icinga action");
			$this->assertEquals(count($directDBList_all),count($json["users"]),"Usercount fetched by icinga doesn't match db-count");
			foreach($directDBList_all as $nr=>$entry) {

				// search entry in json list
				$jsonPendant = null;
				foreach($json["users"] as $user) {
					if($user["user_id"] == $entry["user_id"]){
						$jsonPendant = $user;
						break;
					}
				}
				$this->assertNotNull($jsonPendant,"Couldn't find user ".$entry["user_name"]." in icinga view");				

				foreach($entry as $field=>$value) {
					if($field == 'user_password')
						continue;

					$this->assertEquals($entry[$field],$jsonPendant[$field],"Entry for user ".$entry["user_name"]." is not equal with icinga result");
				}
			}
			success("\tSuccess - result matches!\n");
			return $id; // pass user id
		} catch(Exception $e) {
			$this->fail("Selecting user failed: ".$e->getMessage());
		}
	}
	
	
	/**
	 * @depends testUserSelect
	 */
	public function testUserAlter($id) {
		try {
			info("\tTesting icinga-web action : Edit user\n");
			$context = AgaviContext::getInstance();
			
			$alterParams = new AgaviRequestDataHolder();
			foreach($this->alterParams as $field=>$value) {
				
				$alterParams->setParameter($field,$value);
			}

			$alterParams->setParameter("id",$id);
			$alterParams->setParameter("user_id",$id);
			$modifyAction = $context->getController()->createExecutionContainer("AppKit","Admin.Users.Edit",$alterParams,"simple","write");
			$result = $modifyAction->execute();
			
			$this->assertNotEquals($result->getHttpStatusCode(),"404","Action for editing users not found");				

			// check result
			$result = Doctrine_Core::getTable("NsmUser")->findBy("user_id",$id)->getFirst();
			$this->assertNotNull($result,"User could not be found. This really shouldn't happen at this point..");
			$result = $result->toArray();
			foreach($this->alterParams as $name=>$param) {
				if(!isset($result[$name]))
					continue;
	
				$this->assertEquals($result[$name],$param,"Edited value has not been accepted ");
			}
			success("\tAlter user succeeded!\n");
			return $id;
		} catch(Exception $e) {
			$this->fail("Altering users failed: ".$e->getMessage());
		}
	}
	
	/**
	 * 
	 * @depends testUserAlter
	 */
	public function testUserPreferenceAdd($userid) {
		try {
			info("\tTesting icinga-web action: Set user preference\n");
			$context = AgaviContext::getInstance();
			
			$preferenceData = new AgaviRequestDataHolder();
				$preferenceData->setParameter("params[0][isLong]",false);
				$preferenceData->setParameter("params[0][upref_key]","TEST");
				$preferenceData->setParameter("params[0][upref_val]","TESTCASE");
			
			$modifyAction = $context->getController()->createExecutionContainer("AppKit","User.Preferences",$preferenceData,"simple","write");
			$this->assertType("AgaviExecutionContainer",$modifyAction,"Couldn't create add-preference action");
			
			$result = $modifyAction->execute();		
			echo $result->getContent();		$this->assertNotEquals($result->getHttpStatusCode(),"404","Action for editing users not found");				
		
			// Check if the entry has been written
			$result = Doctrine_Core::getTable("NsmUserPreference")->findAll();
			foreach($result as $res) {
				print_r($result->toArray());
			}
		
			return $userid;
		} catch(Exception $e) {
			$this->fail("Setting an user preference failed!".$e->getMessage());
		}
	}
	
	/**
	 * 
	 * @depends testUserPreferenceAdd
	 */
	public function testUserPreferenceRemove($userid) {
		info("Testing preferences");
		
		return $userid;
	}
	
	
	/*public function testUserDelete($user) {
		
	}*/

	/**

	public function testPrincipalRead($id) {
		info("Testing principalList");
	}
	public function testPrincipalAdd() {}
	public function testPrincipalAlter() {}
	public function testPrincipalRemove() {}
	
	public function testRoleSelect() {}
	public function testRoleAdd() {}
	public function testRoleRemove() {}
	
	public function testRoleAssign() {}
	public function testRoleDeassign() {}
	
	public function testPrincipalAddToUser() {} 
	public function testPrincipalRemoveFromUser() {}
*/	
	public static function tearDownAfterClass() {
		Doctrine_Manager::connection()->rollback();
	}
	
}