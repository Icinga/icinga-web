<?php
/**
* @depends agaviBootstrapTest::testBootstrap 
*/	
class icingaUserOperations extends PHPUnit_Framework_TestCase {
	/**
	* @depends agaviBootstrapTest::testBootstrap 
	* @group Database
	*/	
	public static function setUpBeforeClass() {
		try {
			Doctrine_Manager::connection()->beginTransaction();
			$context = AgaviContext::getInstance();
			$context->getUser()->addCredential("appkit.admin");
			$context->getUser()->addCredential("appkit.admin.users");
			$context->getUser()->addCredential("appkit.admin.groups");
			$context->getUser()->addCredential("icinga.user");
			$context->getUser()->setAuthenticated(true);
		} catch(Exception $e) {
			error("Exception on connection retrieval: ".$e->getMessage()."\n");
		}
	}

	public static $idFixture;
		
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
		"user_disabled" => 1,
		"userroles" => array("3" => "3")
	);
	
	
	
	/**
	 * @depends icingaDatabaseAccessibleTest::testInsert
	 * @group Database
	 */
	public function testUserAdd() {
		try {
			info("Testing actions for user modification\n");
			info("\tTesting admin 'Add user' action\n");
			$context = AgaviContext::getInstance();
			$arguments = new AgaviRequestDataHolder();
			$user = $this->params;
			foreach($user as $name=>$value) {
				$arguments->setParameter($name,$value);							
			}
			
			// insert user
			$container = $context->getController()->createExecutionContainer("AppKit","Admin.Users.Edit",$arguments,"simple","write");
			IcingaWebTestTool::assertInstanceOf("AgaviExecutionContainer",$container,"Couldn't create add-user action");
			
			$result = $container->execute();
			$this->assertNotEquals($result->getHttpStatusCode(),"404","Action for adding users not found");				

			// Check if user is really added
			$result = Doctrine_Core::getTable("NsmUser")->findBy("user_name",$user["user_name"])->getFirst();		
			IcingaWebTestTool::assertInstanceOf("NsmUser",$result,"No user found, something seemed to go wrong");
			success("\tCreated user exists!\n");
			self::$idFixture = $result->get("user_id");
			return true;
		} catch(Exception $e) {
			$this->fail("Adding a new user failed : ".$e->getMessage());
		}
		return true;

	}
	
	/**
	 * @depends testUserAdd
	 * @group Database
	 */
	public function testUserSelect() {
		try {
			$id = self::$idFixture;
			info("\tTesting icinga-web action : List users\n");
			$context = AgaviContext::getInstance();
			$directDBList_all = Doctrine_Query::create()->select("n.*,pr.*,r.*")->from("NsmUser n")->leftJoin("n.NsmPrincipal pr")->leftJoin("n.NsmUserRole r")->execute()->toArray(true);
			$this->assertGreaterThan(0,count($directDBList_all),"A really strange error occured - suddenly couldn't fetch any users");
			
			$sortOrder = new AgaviRequestDataHolder();
			$sortOrder->setParameter("sort","user_id");
			$sortOrder->setParameter("hideDisabled","false");
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
			return true; // pass user id
		} catch(Exception $e) {
			$this->fail("Selecting user failed: ".$e->getMessage());
		}
	}
	
	
	/**
	 * @depends testUserSelect
	 * @group Database
	 */
	public function testUserAlter() {
		try {
			$id = self::$idFixture;
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
			$user = Doctrine_Core::getTable("NsmUser")->findBy("user_id",$id)->getFirst();
			$this->assertNotNull($user,"User could not be found. This really shouldn't happen at this point..");
			$result = $user->toArray();
			foreach($this->alterParams as $name=>$param) {
				if(!isset($result[$name]))
					continue;
	
				$this->assertEquals($result[$name],$param,"Edited value has not been accepted ");
			}
			success("\tAlter user succeeded!\n");
			// Set created user as current user
			$context->getUser()->setAttributeByRef(AppKitSecurityUser::USEROBJ_ATTRIBUTE,$user);
			return true;
		} catch(Exception $e) {
			$this->fail("Altering users failed: ".$e->getMessage());
		}
	}
	
	/**
	 * @depends testUserAlter
	 * @group Database
	 */
	public function testUserPreferenceAdd() {
		try {
			$userid = self::$idFixture;
			info("\tTesting icinga-web action: Set user preference\n");
			$context = AgaviContext::getInstance();
			
			$preferenceData = new AgaviRequestDataHolder();
			$preferenceData->setParameter("params",array("0"=>array("isLong"=>"false","upref_key"=>"TEST","upref_val"=>"TESTCASE")));
	
			$modifyAction = $context->getController()->createExecutionContainer("AppKit","User.Preferences",$preferenceData,"simple","write");
			IcingaWebTestTool::assertInstanceOf("AgaviExecutionContainer",$modifyAction,"Couldn't create add-preference action");
			
			$result = $modifyAction->execute();		
			$this->assertNotEquals($result->getHttpStatusCode(),"404","Action for editing users not found");				
		
			// Check if the entry has been written
			$result = Doctrine_Query::create()
									->select("pr.*, n.user_id")
									->from("NsmUserPreference pr")
									->innerJoin("pr.NsmUser n")
									->where("n.user_id = ?",$userid)
									->execute();			
			$this->assertNotNull($result->getFirst(),"Preference could not be set!");
			success("\tSetting preferences suceeded!\n");
			return true;
		} catch(Exception $e) {
			$this->fail("Setting an user preference failed!".$e->getMessage());
		}
	}
	
	/**
	 * @depends testUserPreferenceAdd
	 * @group Database
	 */
	public function testUserPreferenceRemove() {	
		try {
			$userid = self::$idFixture;
			info("\tTesting icinga-web action: Remove user preference\n");
			$context = AgaviContext::getInstance();

			$preferenceData = new AgaviRequestDataHolder();
			$preferenceData->setParameters(array("remove"=>"true","isLong"=>"false","upref_key"=>"TEST","upref_val"=>"TESTCASE"));
	
			$modifyAction = $context->getController()->createExecutionContainer("AppKit","User.Preferences",$preferenceData,"simple","write");
			IcingaWebTestTool::assertInstanceOf("AgaviExecutionContainer",$modifyAction,"Couldn't create add-preference action");
			
			$result = $modifyAction->execute();		
	
			$this->assertNotEquals($result->getHttpStatusCode(),"404","Action for eediting user preferences not found");				
			
			success("\tDeleting preferences suceeded!\n");
			return true;
		} catch(Exception $e) {
			$this->fail("Removing an user preference failed!".$e->getMessage());
		}
	}
	
	
	/** 
	 * @depends testUserAlter
	 * @group Database
	 */
	public function testPrincipalAdd() {
		try {
			$userid = self::$idFixture;
			info("\tTesting icinga-web action: Add principal\n");
			$context = AgaviContext::getInstance();

			$params = array("2" => array("name" => array("IcingaServicegroup"),"set"=>array("1")));
			$alterParams = new AgaviRequestDataHolder();
			foreach($this->alterParams as $field=>$value) {
				$alterParams->setParameter($field,$value);
			}

			$alterParams->setParameter("id",$userid);
			$alterParams->setParameter("user_id",$userid);
			$alterParams->setParameter("principal_target",$params);
		
			$modifyAction = $context->getController()->createExecutionContainer("AppKit","Admin.Users.Edit",$alterParams,"simple","write");
			$result = $modifyAction->execute();			
			
			$this->assertNotEquals($result->getHttpStatusCode(),"404","Action for editing users not found");				
			$user = Doctrine_Core::getTable("NsmUser")->findBy("user_id",$userid)->getFirst();	
		
			$this->assertArrayHasKey("2",$user->getTargets()->toArray(),"Setting Principal failed");
			success("\tAdding principals suceeded!\n");
			return true;
		} catch(Exception $e) {
			$this->fail("Adding a principal failed!".$e->getMessage());
		}
	}
	
	/** 
	 * @depends testPrincipalAdd
	 * @group Database
	 */
	public function testPrincipalRead() {
		try {
			$userid = self::$idFixture;
			info("\tTesting icinga-web action: Reading principals\n");
			$context = AgaviContext::getInstance();
			$readParams = new AgaviRequestDataHolder();
			$readParams->setParameter("userId",$userid);
			$modifyAction = $context->getController()->createExecutionContainer("AppKit","DataProvider.PrincipalProvider",$readParams,"json","read");
			$result = $modifyAction->execute();			
			$this->assertNotEquals($result->getHttpStatusCode(),"404","Action for reading principals not found");				
			$this->assertArrayHasKey("IcingaServicegroup",json_decode($result->getContent(),true),"Read didn't return previously added principal!");
			success("\tReading principals suceeded!\n");
		} catch(Exception $e) {
			$this->fail("Adding a principal failed!".$e->getMessage());
		}
	}
	
	/**
	 * @depends testUserAdd
	 * @group Database
	 */
	public function testUserDelete() {
		$user = self::$idFixture;	
		$this->markTestSkipped("User delete test doesn't work with transactions yet.");
		try {
			info("\tTesting icinga-web action: Remove user\n");
			$context = AgaviContext::getInstance();

			$params = new AgaviRequestDataHolder();
			$params->setParameter("user_id",array($user => $user));
			$userRemoveAction = $context->getController()->createExecutionContainer("AppKit","Admin.Users.Remove",$params,null,"write");
			$result = $userRemoveAction->execute();
			$this->assertNotEquals($result->getHttpStatusCode(),"404","Action for deleting user not found");				
			
			// Check if group is really deleted
			$result = Doctrine_Core::getTable("NsmUser")->findBy("user_id",$user)->getFirst();		
			$this->assertNotType("NsmUser",$result,"User found despite deleting it, something seemed to go wrong");
			success("\tReading user suceeded!\n");
		} catch(Exception $e) {
			$this->fail("Removing a user failed!".$e->getMessage());
		}		
	}
	
    /** 
    * @group Database
    */
	public static function tearDownAfterClass() {
		try {	
			Doctrine_Manager::connection()->rollback();
		} catch(Exception $e) {
			info("Rollback failed, check for previous errors \n");

		}
	}
	
}
