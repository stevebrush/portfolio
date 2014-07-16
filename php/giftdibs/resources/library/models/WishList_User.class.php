<?php
class WishList_User extends DatabaseObject {
	
	protected $tableName = "WishList_User";
	
	protected $tableFields = array(
		"wishListUserId",
		"wishListId",
		"userId"
	);
	
	protected $wishListUserId,
		$wishListId,
		$userId;
	
}