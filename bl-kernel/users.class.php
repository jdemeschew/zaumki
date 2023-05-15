<?php defined('BLUDIT') or die('Bludit CMS.');

class Users extends dbJSON {

	protected $dbFields = array(
		'firstName'=>'',
		'lastName'=>'',
		'nickname'=>'',
		'bio'=>'',
		'role'=>'author', // admin, editor, author
		'password'=>'',
		'salt'=>'!Pink Floyd!Welcome to the machine!',
		'email'=>'',
		'registered'=>'1985-03-15 10:00',
		'tokenRemember'=>'',
		'tokenAuth'=>'',
		'tokenAuthTTL'=>'2009-03-15 14:00',
		'twitter'=>'',
		'facebook'=>'',
		'codepen'=>'',
		'instagram'=>'',
		'github'=>'',
		'gitlab'=>'',
		'linkedin'=>'',
		'mastodon'=>'',
		'vk'=>'',
		'youtube'=>'',
		'discord'=>''
	);

	function __construct()
	{
		parent::__construct(DB_USERS);
	}

	public function getDefaultFields(): array
	{
		return $this->dbFields;
	}

	// Return an array with the database of the user, FALSE otherwise
	public function getUserDB($username)
	{
		if ($this->exists($username)) {
			return $this->db[$username];
		}
		return false;
	}

	// Return TRUE if the user exists, FALSE otherwise
	public function exists($username): bool
	{
		return isset($this->db[$username]);
	}

	/**
	 * Disable an username
	 */
	public function disableUser(string $username): string
	{
		$this->db[$username]['password'] = '!';
		$this->save();
		return $username;
	}

	/**
	 * Create a new user. === Bludit v4
	 * @param array $args All supported parameters are defined in this class, variable $dbFields
	 * @return string|bool Returns the username on successful create, FALSE otherwise
	 */
	public function add($args)
	{
		// The username is store as key and not as field
		$username = $args['username'];

		if ($this->exists($username)) {
			Log::set(__METHOD__.LOG_SEP.'The username already exists. Username: '.$username, LOG_TYPE_ERROR);
			return false;
		}

		// The password is hashed, the password doesn't need to be sanitize in the next step
		$password = $args['password'];

		$row = array();
		foreach ($this->dbFields as $field=>$value) {
			if (isset($args[$field])) {
				$finalValue = $args[$field];
				// Remove HTML and PHP tags
				$finalValue = Sanitize::removeTags($finalValue);
				// Sanitize if will be stored on database
				$finalValue = Sanitize::html($finalValue);
			} else {
				// Default value for the field if not defined
				$finalValue = $value;
			}
			settype($finalValue, gettype($value));
			$row[$field] = $finalValue;
		}

		$row['registered'] = Date::current(DB_DATE_FORMAT);
		$row['salt'] = $this->generateSalt();
		$row['password'] = $this->generatePasswordHash($password, $row['salt']);
		$row['tokenAuth'] = $this->generateAuthToken();

		// Save the database
		$this->db[$username] = $row;
		$this->save();
		return $username;
	}

	/**
	 * Edit an user. === Bludit v4
	 * @param		array		$args		All supported parameters are defined in this class, variable $dbFields
	 * @return		string|bool				Returns the username on successful edit, FALSE otherwise
	 */
	public function edit($args)
	{
		// The username is store as key and not as field
		$username = $args['username'];

		// The following fields can not be changed via edit the user
		unset($args['salt']);
		unset($args['tokenAuth']);
		unset($args['registered']);

		// Current database of the user
		$row = $this->db[$username];
		foreach ($this->dbFields as $field=>$value) {
			if ($field!=='password') {
				if (isset($args[$field])) {
					$finalValue = $args[$field];
					// Remove HTML and PHP tags
					$finalValue = Sanitize::removeTags($finalValue);
					// Sanitize if will be stored on database
					$finalValue = Sanitize::html($finalValue);
				} else {
					// Default value is the current one
					$finalValue = $row[$field];
				}
				settype($finalValue, gettype($value));
				$row[$field] = $finalValue;
			}
		}

		// Set a new password
		if (!empty($args['password'])) {
			$row['salt'] = $this->generateSalt();
			$row['password'] = $this->generatePasswordHash($args['password'], $row['salt']);
			$row['tokenAuth'] = $this->generateAuthToken();
		}

		// Save the database
		$this->db[$username] = $row;
		$this->save();
		return $username;
	}

	/**
	 * Delete an user. === Bludit v4
	 * @param		string		$username	Username to be delete
	 * @return		string|bool				Returns true or false
	 */
	public function delete($username)
	{
		unset($this->db[$username]);
		return $this->save();
	}

	public function generateAuthToken()
	{
		return md5( uniqid().time().DOMAIN );
	}

	public function generateRememberToken()
	{
		return $this->generateAuthToken();
	}

	public function generateSalt()
	{
		return Text::randomText(SALT_LENGTH);
	}

	public function generatePasswordHash($password, $salt)
	{
		return sha1($password.$salt);
	}

	public function setRememberToken($username, $token)
	{
		$args['username'] = $username;
		$args['tokenRemember'] = $token;
		return $this->edit($args);
	}

	// Change user password
	// args => array( username, password )
	public function setPassword($args)
	{
		return $this->edit($args);
	}

	// Return the username associated to an email, FALSE otherwise
	public function getByEmail($email)
	{
		foreach ($this->db as $username=>$values) {
			if ($values['email']==$email) {
				return $username;
			}
		}
		return false;
	}

	// Returns the username with the authentication token assigned, FALSE otherwise
	public function getByAuthToken($token)
	{
		foreach ($this->db as $username=>$fields) {
			if ($fields['tokenAuth']==$token) {
				return $username;
			}
		}
		return false;
	}

	// Returns the username with the remember token assigned, FALSE otherwise
	public function getByRememberToken($token)
	{
		foreach ($this->db as $username=>$fields) {
			if (!empty($fields['tokenRemember'])) {
				if ($fields['tokenRemember']==$token) {
					return $username;
				}
			}
		}
		return false;
	}

	// This function clean all tokens for Remember me
	// This function is used when some hacker try to use an invalid remember token
	public function invalidateAllRememberTokens()
	{
		foreach ($this->db as $username=>$values) {
			$this->db[$username]['tokenRemember'] = '';
		}
		return $this->save();
	}

	public function keys()
	{
		return array_keys($this->db);
	}
}
