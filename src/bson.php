<?hh
use Mongofill\Protocol;
use Mongofill\Socket;



/**
 * Deserializes a BSON object into a PHP array
 *
 * @param string $bson - The BSON to be deserialized.
 *
 * @return array - Returns the deserialized BSON object.
 */
<<__Native>>
function bson_decode(string $bson): array;

/**
 * Serializes a PHP variable into a BSON string
 *
 * @param mixed $anything - The variable to be serialized.
 *
 * @return string - Returns the serialized string.
 */
<<__Native>>
function bson_encode(mixed $anything): string;

/**
 * Deserializes multiple BSON objects into a nested PHP array
 *
 * @param string $bson - The BSON to be deserialized.
 *
 * @return array - Returns an array of deserialized BSON object.
 */
<<__Native>>
function bson_decode_multiple(string $bson): array;

/**
 * Serializes an PHP array of variable into BSON documents
 *
 * @param array $documents - The documents to be serialized.
 *
 * @return string - Returns the serialized string.
 */
<<__Native>>
function bson_encode_multiple(array $documents): string;


/**
 * A connection between PHP and MongoDB.   
 * 
 * This class extends MongoClient and provides access to several deprecated 
 * methods.
 */
class Mongo extends MongoClient
{
    /**
     * Connects with a database server
     *
     * @return bool - If the connection was successful.
     */
    protected function connectUtil()
    {
        throw new Exception('Not Implemented');
    }

    /**
     * Get pool size for connection pools
     *
     * @return int - Returns the current pool size.
     */
    public static function getPoolSize()
    {
        throw new Exception('Not Implemented');
    }

    /**
     * Returns the address being used by this for slaveOkay reads
     *
     * @return string - The address of the secondary this connection is
     *   using for reads.   This returns NULL if this is not connected to a
     *   replica set or not yet initialized.
     */
    public function getSlave()
    {
        throw new Exception('Not Implemented');
    }

    /**
     * Get slaveOkay setting for this connection
     *
     * @return bool - Returns the value of slaveOkay for this instance.
     */
    public function getSlaveOkay()
    {
        throw new Exception('Not Implemented');
    }

    /**
     * Returns information about all connection pools.
     *
     * @return array - Each connection pool has an identifier, which starts
     *   with the host.
     */
    public function poolDebug()
    {
        throw new Exception('Not Implemented');
    }

    /**
     * Set the size for future connection pools.
     *
     * @param int $size - The max number of connections future pools will
     *   be able to create. Negative numbers mean that the pool will spawn an
     *   infinite number of connections.
     *
     * @return bool - Returns the former value of pool size.
     */
    public static function setPoolSize($size)
    {
        throw new Exception('Not Implemented');
    }

    /**
     * Change slaveOkay setting for this connection
     *
     * @param bool $ok - If reads should be sent to secondary members of a
     *   replica set for all possible queries using this MongoClient
     *   instance.
     *
     * @return bool - Returns the former value of slaveOkay for this
     *   instance.
     */
    public function setSlaveOkay($ok = true)
    {
        throw new Exception('Not Implemented');
    }

    /**
     * Choose a new secondary for slaveOkay reads
     *
     * @return string - The address of the secondary this connection is
     *   using for reads.
     */
    public function switchSlave()
    {
        throw new Exception('Not Implemented');
    }
}



/**
 * An object that can be used to store or retrieve binary data from the
 * database. 
 */
class MongoBinData
{
    const GENERIC = 0;
    const FUNC = 1;
    const BYTE_ARRAY = 2;
    const UUID = 3;
    const UUID_RFC4122 = 4;
    const MD5 = 5;
    const CUSTOM = 128;

    /**
     * @var string
     */
    public $bin;

    /**
     * @var int
     */
    public $type;

    /**
     * Creates a new binary data object.
     *
     * @param string $data - Binary data.
     * @param int $type - Data type.
     *
     * @return  - Returns a new binary data object.
     */
    public function __construct($data, $type = self::BYTE_ARRAY)
    {
        $this->bin = $data;
        $this->type = $type;
    }

    /**
     * The string representation of this binary data object.
     *
     * @return string - Returns the string "Mongo Binary Data". To access
     *   the contents of a MongoBinData, use the bin field.
     */
    public function __toString()
    {
        return "<Mongo Binary Data>";
    }
}






/**
 * A connection manager for PHP and MongoDB.
 */
class MongoClient
{
    const VERSION = '1.3.0-mongofill';
    const DEFAULT_HOST = 'localhost';
    const DEFAULT_PORT = 27017;
    const RP_PRIMARY   = 'primary';
    const RP_PRIMARY_PREFERRED = 'primaryPreferred';
    const RP_SECONDARY = 'secondary';
    const RP_SECONDARY_PREFERRED = 'secondaryPreferred';
    const RP_NEAREST   = 'nearest';

    /**
     * @var boolean
     */
    public $connected;

    /**
     * @var string
     */
    public $boolean = false;

    /**
     * @var string
     */
    public $status;
    
    /**
     * @var string
     */
    public $server;
    
    /**
     * @var boolean
     */
    public $persistent;

    /**
     * @var array
     */
    private $options;

    /**
     * @var string
     */
    private $host = self::DEFAULT_HOST;

    /**
     * @var int
     */
    private $port = self::DEFAULT_PORT;

    /**
     * @var Protocol
     */
    private $protocol;

    /**
     * @var resource
     */
    private $socket;

    /**
     * @var array
     */
    private $databases = [];

    /**
     * Creates a new database connection object
     *
     * @param string $server - The server name.
     * @param string $options - An array of options for the connection. 
     *
     * @return array - Returns the database response.
     */
    public function __construct($server = 'mongodb://localhost:27017', array $options = [])
    {
        if (!$options) {
            $options = ['connect' => true];
        }

        $this->options = $options;
        if (preg_match('/mongodb:\/\/([0-9a-zA-Z_.-]+)(:(\d+))?/', $server, $matches)) {
            $this->host = $matches[1];
            if (isset($matches[3])) {
                $this->port = $matches[3];
            }
        } else {
            $this->host = $server;
        }

        if (isset($options['port'])) {
            $this->port = $options['port'];
        }

        $this->socket = new Socket($this->host, $this->port);
        $this->server = "mongodb://{$this->host}:{$this->port}";

        if (isset($options['connect']) && $options['connect']) {
            $this->connect();
        }
    }

    /**
     * Gets a database
     *
     * @param string $dbname - The database name.
     *
     * @return MongoDB - Returns a new db object.
     */
    public function __get($dbname)
    {
        return $this->selectDB($dbname);
    }

    /**
     * Connects to a database server
     *
     * @return bool - If the connection was successful.
     */
    public function connect()
    {
        if ($this->protocol) {
            return true;
        }

        $this->socket->connect();
        $this->protocol = new Protocol($this->socket);

        return true;
    }

    /**
     * Closes this connection
     *
     * @param boolean|string $connection - If connection is not given, or
     *   FALSE then connection that would be selected for writes would be
     *   closed. In a single-node configuration, that is then the whole
     *   connection, but if you are connected to a replica set, close() will
     *   only close the connection to the primary server.
     *
     * @return bool - Returns if the connection was successfully closed.
     */
    public function close($connection = null)
    {
        if ($this->socket) {
            $this->socket->disconnect();
            $this->protocol = null;
        }

        //TODO: implement $connection handling
    }

    /**
     * @return Protocol
     */
    public function _getProtocol()
    {
        if (!$this->connected) {
            $this->connect();
        }

        return $this->protocol;
    }

    /**
     * Gets a database
     *
     * @param string $name - The database name.
     *
     * @return MongoDB - Returns a new database object.
     */
    public function selectDB($name)
    {
        if (!isset($this->databases[$name])) {
            $this->databases[$name] = new MongoDB($this, $name);
        }

        return $this->databases[$name];
    }

    /**
     * Gets a database collection
     *
     * @param string $db - The database name.
     * @param string $collection - The collection name.
     *
     * @return MongoCollection - Returns a new collection object.
     */
    public function selectCollection($db, $collection)
    {
        return $this->selectDB($db)->selectCollection($collection);
    }

    /**
     * Drops a database [deprecated]
     *
     * @param mixed $db - The database to drop. Can be a MongoDB object or
     *   the name of the database.
     *
     * @return array - Returns the database response.
     */
    public function dropDB($db)
    {
        throw new Exception('Not Implemented');
    }

    /**
     * Return info about all open connections
     *
     * @return array - An array of open connections.
     */
    public static function getConnections()
    {
        throw new Exception('Not Implemented');
    }

    /**
     * Updates status for all associated hosts
     *
     * @return array - Returns an array of information about the hosts in
     *   the set. Includes each host's hostname, its health (1 is healthy),
     *   its state (1 is primary, 2 is secondary, 0 is anything else), the
     *   amount of time it took to ping the server, and when the last ping
     *   occurred. For example, on a three-member replica set, it might look
     *   something like:
     */
    public function getHosts()
    {
        throw new Exception('Not Implemented');
    }

    /**
     * Get the read preference for this connection
     *
     * @return array -
     */
    public function getReadPreference()
    {
        throw new Exception('Not Implemented');
    }

    /**
     * Kills a specific cursor on the server
     *
     * @param string $serverHash - The server hash that has the cursor.
     *   This can be obtained through MongoCursor::info.
     * @param int|mongoint64 $id - The ID of the cursor to kill. You can
     *   either supply an int containing the 64 bit cursor ID, or an object
     *   of the MongoInt64 class. The latter is necessary on 32 bit platforms
     *   (and Windows).
     *
     * @return bool - Returns TRUE if the method attempted to kill a
     *   cursor, and FALSE if there was something wrong with the arguments
     *   (such as a wrong server_hash). The return status does not reflect
     *   where the cursor was actually killed as the server does not provide
     *   that information.
     */
    public function killCursor($serverHash, $id)
    {
        // since we currently support just single server connection,
        // the $serverHash arg is ignored

        if ($id instanceof MongoInt64) {
            $id = $id->value;
        } elseif (!is_numeric($id)) {
            return false;
        }

        $this->protocol->opKillCursors([ (int)$id ], [], MongoCursor::$timeout);

        return true;
    }

    /**
     * Lists all of the databases available.
     *
     * @return array - Returns an associative array containing three
     *   fields. The first field is databases, which in turn contains an
     *   array. Each element of the array is an associative array
     *   corresponding to a database, giving th database's name, size, and if
     *   it's empty. The other two fields are totalSize (in bytes) and ok,
     *   which is 1 if this method ran successfully.
     */
    public function listDBs()
    {
        throw new Exception('Not Implemented');
    }

    /**
     * Set the read preference for this connection
     *
     * @param string $readPreference -
     * @param array $tags -
     *
     * @return bool -
     */
    public function setReadPreference($readPreference, array $tags = null)
    {
        throw new Exception('Not Implemented');
    }

    /**
     * String representation of this connection
     *
     * @return string - Returns hostname and port for this connection.
     */
    public function __toString()
    {
        return (string) $this->host . ':' . (string) $this->port;
    }
}



/**
 * Represents JavaScript code for the database.
 */
class MongoCode
{
    /**
     * @var string
     */
    private $code;

    /**
     * @var array
     */
    private $scope;

    /**
     * Creates a new code object
     *
     * @param string $code - A string of code.
     * @param array $scope - The scope to use for the code.
     *
     * @return  - Returns a new code object.
     */
    public function __construct($code, array $scope = [])
    {
        $this->code = (string) $code;
        $this->scope = $scope;
    }

    public function getScope()
    {
        return $this->scope;
    }

    /**
     * Returns this code as a string
     *
     * @return string - This code, the scope is not returned.
     */
    public function __toString()
    {
        return $this->code;
    }
}




/**
 * Represents a MongoDB collection.
 */
class MongoCollection
{
    /**
     * @var string
     */
    private $fqn;

    /**
     * @var string
     */
    private $name;

    /**
     * @var MongoDB
     */
    private $db;

    /**
     * @var MongoClient
     */
    private $client;

    /**
     * @var Protocol
     */
    private $protocol;

    /**
     * Creates a new collection
     *
     * @param MongoDB $db   - Parent database.
     * @param string  $name -
     *
     * @return - Returns a new collection object.
     */
    public function __construct(MongoDB $db, $name)
    {
        $this->db = $db;
        $this->name = $name;
        $this->fqn = $db->_getFullCollectionName($name);
        $this->client = $db->_getClient();
        $this->protocol = $this->client->_getProtocol();
    }

    /**
     * Gets a collection
     *
     * @param string $name - The next string in the collection name.
     *
     * @return MongoCollection - Returns the collection.
     */
    public function __get($name)
    {
        return $this->db->selectCollection($this->name . '.' . $name);
    }

    /**
     * Counts the number of documents in this collection
     *
     * @param array $query - Associative array or object with fields to
     *   match.
     * @param int $limit - Specifies an upper limit to the number returned.
     * @param int $skip  - Specifies a number of results to skip before
     *   starting the count.
     *
     * @return int - Returns the number of documents matching the query.
     */
    public function count(array $query = [], $limit = 0, $skip = 0)
    {
        $cmd = [
            'count' => $this->name,
            'query' => $query
        ];

        if ($limit) {
            $cmd['limit'] = $limit;
        }

        if ($skip) {
            $cmd['skip'] = $skip;
        }

        $result = $this->db->command($cmd);

        if (isset($result['ok'])) {
            return (int) $result['n'];
        }

        return false;
    }

    /**
     * Creates a database reference
     *
     * @param mixed $documentOrId - If an array or object is given, its
     *   _id field will be used as the reference ID. If a MongoId or scalar
     *   is given, it will be used as the reference ID.
     *
     * @return array - Returns a database reference array.   If an array
     *   without an _id field was provided as the document_or_id parameter,
     *   NULL will be returned.
     */
    public function createDBRef($documentOrId)
    {
        throw new Exception('Not Implemented');
    }

    /**
     * Fetches the document pointed to by a database reference
     *
     * @param array $ref - A database reference.
     *
     * @return array - Returns the database document pointed to by the reference.
     */
    public function getDBRef(array $ref)
    {
        throw new Exception('Not Implemented');
    }

    /**
     * Queries this collection, returning a for the result set
     *
     * @param array $query - The fields for which to search. MongoDB's
     *   query language is quite extensive.
     * @param array $fields - Fields of the results to return.
     *
     * @return MongoCursor - Returns a cursor for the search results.
     */
    public function find(array $query = [], array $fields = [])
    {
        return new MongoCursor($this->client, $this->fqn, $query, $fields);
    }

    /**
     * Queries this collection, returning a single element
     *
     * @param array $query - The fields for which to search. MongoDB's
     *   query language is quite extensive.
     * @param array $fields - Fields of the results to return.
     *
     * @return array - Returns record matching the search or NULL.
     */
    public function findOne($query = [], array $fields = [])
    {
        $cur = $this->find($query, $fields)->limit(1);

        return ($cur->valid()) ? $cur->current() : null;
    }

    /**
     * Update a document and return it
     *
     * @param array $query   -
     * @param array $update  -
     * @param array $fields  -
     * @param array $options -
     *
     * @return array - Returns the original document, or the modified
     *   document when new is set.
     */
    public function findAndModify(
        array $query, array $update = null, array $fields = null, array $options
    )
    {
        $command = ['findandmodify' => $this->name];

        if ($query) {
            $command['query'] = $query;
        }

        if ($update) {
            $command['update'] = $update;
        }

        if ($fields) {
            $command['fields'] = $fields;
        }

        $command = array_merge($command, $options);
        $result = $this->db->command($command);
        
        if (isset($result['value'])) {
            return $result['value'];
        }
        return null;
    }

    /**
     * Drops this collection
     *
     * @return array - Returns the database response.
     */
    public function drop()
    {
        $this->db->command(['drop' => $this->name]);
    }

    /**
     * Returns this collections name
     *
     * @return string - Returns the name of this collection.
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Inserts a document into the collection
     *
     * @param array|object $a - An array or object. If an object is used,
     *   it may not have protected or private properties.    If the parameter
     *   does not have an _id key or property, a new MongoId instance will be
     *   created and assigned to it. This special behavior does not mean that
     *   the parameter is passed by reference.
     * @param array $options - Options for the insert.
     *
     * @return bool|array - Returns an array containing the status of the
     *   insertion if the "w" option is set.
     */
    public function insert(&$document, array $options = [])
    {
        $timeout = MongoCursor::$timeout;
        if (!empty($options['timeout'])) {
            $timeout = $options['timeout'];
        }

        $this->fillIdInDocumentIfNeeded($document);
        $documents = [&$document];

        return $this->protocol->opInsert(
            $this->fqn, 
            $documents,
            $options,
            $timeout
        );
    }

    /**
     * Inserts multiple documents into this collection
     *
     * @param array $a       - An array of arrays or objects.
     * @param array $options - Options for the inserts.
     *
     * @return mixed - If the w parameter is set to acknowledge the write,
     *   returns an associative array with the status of the inserts ("ok")
     *   and any error that may have occurred ("err"). Otherwise, returns
     *   TRUE if the batch insert was successfully sent, FALSE otherwise.
     */
    public function batchInsert(array &$documents, array $options = [])
    {
        $timeout = MongoCursor::$timeout;
        if (!empty($options['timeout'])) {
            $timeout = $options['timeout'];
        }

        $count = count($documents);
        $keys = array_keys($documents);
        for ($i=0; $i < $count; $i++) {
            $this->fillIdInDocumentIfNeeded($documents[$keys[$i]]);
        }

        $this->protocol->opInsert($this->fqn, $documents, $options, $timeout);

        // Fake response for async insert -
        // TODO: detect "w" option and return status array
        return true;
    }

    private function fillIdInDocumentIfNeeded(&$document)
    {
        if (is_object($document)) {
            $document = get_object_vars($document);
        }

        if (!isset($document['_id'])) {
            $document['_id'] = new MongoId();
        }
    }

    /**
     * Update records based on a given criteria
     *
     * @param array $criteria   - Description of the objects to update.
     * @param array $new_object - The object with which to update the
     *   matching records.
     * @param array $options - This parameter is an associative array of
     *   the form array("optionname" => boolean, ...)
     *
     * @return bool|array - Returns an array containing the status of the
     *   update if the "w" option is set. Otherwise, returns TRUE.   Fields
     *   in the status array are described in the documentation for
     *   MongoCollection::insert().
     */
    public function update(array $criteria, array $newObject, array $options = [])
    {
        $timeout = MongoCursor::$timeout;
        if (!empty($options['timeout'])) {
            $timeout = $options['timeout'];
        }

        return $this->protocol->opUpdate(
            $this->fqn,
            $criteria,
            $newObject,
            $options,
            $timeout
        );
    }

    /**
     * Saves a document to this collection
     *
     * @param array|object $a - Array or object to save. If an object is
     *   used, it may not have protected or private properties.
     * @param array $options - Options for the save.
     *
     * @return mixed - If w was set, returns an array containing the status
     *   of the save. Otherwise, returns a boolean representing if the array
     *   was not empty (an empty array will not be inserted).
     */
    public function save($document, array $options = [])
    {
        if (!$document) {
            return false;
        }

        if (is_object($document)) {
            $document = get_object_vars($document);
        }

        if (isset($document['_id'])) {
            return $this->update(['_id' => $document['_id']], $document, $options);
        } else {
            return $this->insert($document, $options);
        }
    }

    /**
     * Remove records from this collection
     *
     * @param array $criteria - Description of records to remove.
     * @param array $options  - Options for remove.    "justOne"   Remove at
     *   most one record matching this criteria.
     *
     * @return bool|array - Returns an array containing the status of the
     *   removal if the "w" option is set. Otherwise, returns TRUE.   Fields
     *   in the status array are described in the documentation for
     *   MongoCollection::insert().
     */
    public function remove(array $criteria = [], array $options = [])
    {
        $timeout = MongoCursor::$timeout;
        if (!empty($options['timeout'])) {
            $timeout = $options['timeout'];
        }

        return $this->protocol->opDelete(
            $this->fqn,
            $criteria,
            $options,
            $timeout
        );
    }

    /**
     * Validates this collection
     *
     * @param bool $scanData - Only validate indices, not the base collection.
     *
     * @return array - Returns the databases evaluation of this object.
     */
    public function validate($scanData = false)
    {
        $result = $this->db->command([
            'validate' => $this->name,
            'full' => $scanData
        ]);

        if ($result) {
            return $result;
        }

        return false;
    }

    /**
     * Converts keys specifying an index to its identifying string
     *
     * @param mixed $keys - Field or fields to convert to the identifying string
     *
     * @return string - Returns a string that describes the index.
     */
    protected static function toIndexString($keys)
    {
        if (is_string($keys)) {
            return self::toIndexStringFromString($keys);
        } elseif (is_object($keys)) {
            $keys = get_object_vars($keys);
        }

        if (is_array($keys)) {
            return self::toIndexStringFromArray($keys);
        }

        trigger_error('MongoCollection::toIndexString(): The key needs to be either a string or an array', E_USER_WARNING);

        return null;
    }

    public static function _toIndexString($keys)
    {
        return self::toIndexString($keys);
    }

    private static function toIndexStringFromString($keys)
    {
        return str_replace('.', '_', $keys . '_1');
    }

    private static function toIndexStringFromArray(array $keys)
    {
        $prefValue = null;
        if (isset($keys['weights'])) {
            $keys = $keys['weights'];
            $prefValue = 'text';
        }

        $keys = (array) $keys;
        foreach ($keys as $key => $value) {
            if ($prefValue) {
                $value = $prefValue;
            }

            $keys[$key] = str_replace('.', '_', $key . '_' . $value);
        }

        return implode('_', $keys);
    }

    /**
     * Deletes an index from this collection
     *
     * @param string|array $keys - Field or fields from which to delete the
     *   index.
     *
     * @return array - Returns the database response.
     */
    public function deleteIndex($keys)
    {
        $cmd = [
            'deleteIndexes' => $this->name,
            'index' => self::toIndexString($keys)
        ];

        return $this->db->command($cmd);
    }

    /**
     * Delete all indices for this collection
     *
     * @return array - Returns the database response.
     */
    public function deleteIndexes()
    {
        return (bool) $this->db->getIndexesCollection()->drop();
    }

    /**
     * Creates an index on the given field(s), or does nothing if the index
     *    already exists
     *
     *
     * @param string|array $key|keys -
     * @param array        $options  - This parameter is an associative array of
     *   the form array("optionname" => boolean, ...).
     *
     * @return bool - Returns an array containing the status of the index
     *   creation if the "w" option is set. Otherwise, returns TRUE.   Fields
     *   in the status array are described in the documentation for
     *   MongoCollection::insert().
     */
    public function ensureIndex($keys, array $options = [])
    {
        if (!is_array($keys)) {
            $keys = [$keys => 1];
        }

        $index = [
            'ns' => $this->fqn,
            'name' => self::toIndexString($keys, $options),
            'key' => $keys
        ];

        $insertOptions = [];
        if (isset($options['safe'])) {
            $insertOptions['safe'] = $options['safe'];
        }

        if (isset($options['w'])) {
            $insertOptions['w'] = $options['w'];
        }

        if (isset($options['fsync'])) {
            $insertOptions['fsync'] = $options['fsync'];
        }

        if (isset($options['timeout'])) {
            $insertOptions['timeout'] = $options['timeout'];
        }

        $index = array_merge($index, $options);

        $return = (bool) $this->db->getIndexesCollection()->insert(
            $index,
            $insertOptions
        );

        return $return;
    }

    /**
     * Returns information about indexes on this collection
     *
     * @return array - This function returns an array in which each element
     *   describes an index. Elements will contain the values name for the
     *   name of the index, ns for the namespace (a combination of the
     *   database and collection name), and key for a list of all fields in
     *   the index and their ordering. Additional values may be present for
     *   special indexes, such as unique or sparse.
     */
    public function getIndexInfo()
    {
        $indexes = $this->db->getIndexesCollection()->find([
            'ns' => $this->fqn
        ]);

        return iterator_to_array($indexes);
    }

    /**
     * Set the read preference for this collection
     *
     * @param string $read_preference -
     * @param array  $tags            -
     *
     * @return bool -
     */
    public function setReadPreference($readPreference, array $tags = null)
    {
        throw new Exception('Not Implemented');
    }

    /**
     * Get the read preference for this collection
     *
     * @return array -
     */
    public function getReadPreference()
    {
        throw new Exception('Not Implemented');
    }

    /**
     * Change slaveOkay setting for this collection
     *
     * @param bool $ok - If reads should be sent to secondary members of a
     *   replica set for all possible queries using this MongoCollection
     *   instance.
     *
     * @return bool - Returns the former value of slaveOkay for this
     *   instance.
     */
    public function setSlaveOkay($ok = true)
    {
        throw new Exception('Not Implemented');
    }

    /**
     * Get slaveOkay setting for this collection
     *
     * @return bool - Returns the value of slaveOkay for this instance.
     */
    public function getSlaveOkay()
    {
        throw new Exception('Not Implemented');
    }

    /**
     * Perform an aggregation using the aggregation framework
     *
     * @param array $pipeline -
     * @param array $op       -
     * @param array $...      -
     *
     * @return array - The result of the aggregation as an array. The ok
     *   will be set to 1 on success, 0 on failure.
     */
    public function aggregate(array $pipeline)
    {
        if (func_num_args() > 1) {
            $pipeline = func_get_args();
        }

        $cmd = [
            'aggregate' => $this->name,
            'pipeline' => $pipeline
        ];

        return $this->db->command($cmd);
    }

    /**
     * Retrieve a list of distinct values for the given key across a collection.
     *
     * @param string $key   -
     * @param array  $query -
     *
     * @return array - Returns an array of distinct values,
     */
    public function distinct($key, array $query = [])
    {
        $cmd = [
            'distinct' => $this->name,
            'key' => $key,
            'query' => $query
        ];

        $results = $this->db->command($cmd);
        if (!isset($results['values'])) {
            return [];
        }

        return $results['values'];
    }

    /**
     * Performs an operation similar to SQL's GROUP BY command
     *
     * @param mixed $keys - Fields to group by. If an array or non-code
     *   object is passed, it will be the key used to group results.
     * @param array $initial - Initial value of the aggregation counter
     *   object.
     * @param mongocode $reduce - A function that takes two arguments (the
     *   current document and the aggregation to this point) and does the
     *   aggregation.
     * @param array $options - Optional parameters to the group command
     *
     * @return array - Returns an array containing the result.
     */
    public function group(
        $keys,
        array $initial,
        $reduce,
        array $options = array()
    )
    {
        $cmd = [
            'group' => [
                'ns' => $this->name,
                'key' => $keys,
                '$reduce' => $reduce,
                'initial' => $initial
            ]
        ];

        if (isset($options['finalize'])) {
            $cmd['group']['finalize'] = $options['finalize'];
        }

        if (isset($options['cond'])) {
            $cmd['group']['cond'] = $options['condition'];
        }

        $results = $this->db->command($cmd);
        if (!isset($results['retval'])) {
            return [];
        }

        return $results['retval'];
    }

    /**
     * String representation of this collection
     *
     * @return string - Returns the full name of this collection.
     */
    public function __toString()
    {
        return $this->fqn;
    }
}



/**
 * Thrown when the driver fails to connect to the database.
 */
class MongoConnectionException extends Exception
{
}




/**
 * A cursor is used to iterate through the results of a database query.
 */
class MongoCursor implements Iterator
{
    const DEFAULT_BATCH_SIZE = 100;
    
    /**
     * @var integer
     */
    public static $timeout = 30000;

    /**
     * @var MongoClient
     */
    private $client;

    /**
     * @var Protocol
     */
    private $protocol;

    /**
     * Full collection name
     * @var string
     */
    private $fcn;

    /**
     * @var array[]
     */
    private $documents = [];

    /**
     * @var int
     */
    private $currKey = 0;

    /**
     * @var null|int
     */
    private $cursorId = null;

    /**
     * @var bool
     */
    private $fetching = false;

    /**
     * @var bool
     */
    private $end = false;

    /**
     * @var bool
     */
    private $hasMore = false;

    /**
     * @var array
     */
    private $query = [];

    /**
     * @var int
     */
    private $queryLimit = 0;

    /**
     * @var int
     */
    private $querySkip = 0;

    /**
     * @var array|null
     */
    private $querySort = null;

    /**
     * @var int
     */
    private $queryTimeout = null;

    /**
     * @var int
     */
    private $batchSize = self::DEFAULT_BATCH_SIZE;

    /**
     * Create a new cursor
     *
     * @param mongoclient $connection - Database connection.
     * @param string      $ns         - Full name of database and collection.
     * @param array       $query      - Database query.
     * @param array       $fields     - Fields to return.
     *
     * @return - Returns the new cursor.
     */
    public function __construct(MongoClient $connection, $ns, array $query = [], array $fields = [])
    {
        $this->client = $connection;
        $this->protocol = $connection->_getProtocol();
        $this->fcn = $ns;
        $this->fields = $fields;
        $this->query['$query'] = $query;
        $this->queryTimeout = self::$timeout;
    }

    /**
     * Clears the cursor
     *
     * @return void - NULL.
     */
    public function reset()
    {
        $this->documents = [];
        $this->currKey = 0;
        $this->cursorId = null;
        $this->end = false; 
        $this->fetching = false;
        $this->hasMore = false;
    }

    /**
     * Gives the database a hint about the query
     *
     * @param mixed $index - Index to use for the query. If a string is
     *   passed, it should correspond to an index name. If an array or object
     *   is passed, it should correspond to the specification used to create
     *   the index (i.e. the first argument to
     *   MongoCollection::ensureIndex()).
     *
     * @return MongoCursor - Returns this cursor.
     */
    public function hint($index)
    {
        if (is_object($index)) {
            $index = get_object_vars($index);
        }

        if (is_array($index)) {
            $index = MongoCollection::_toIndexString($index);
        }

        $this->query['$hint'] = $index;

        return $this;
    }

    /**
     * Use snapshot mode for the query
     *
     * @return MongoCursor - Returns this cursor.
     */
    public function snapshot()
    {
        $this->query['$snapshot'] = true;

        return $this;
    }

    /**
     * Sorts the results by given fields
     *
     * @param array $fields - An array of fields by which to sort. Each
     *   element in the array has as key the field name, and as value either
     *   1 for ascending sort, or -1 for descending sort.
     *
     * @return MongoCursor - Returns the same cursor that this method was
     *   called on.
     */
    public function sort(array $fields)
    {
        $this->query['$orderby'] = $fields;

        return $this;
    }

    /**
     * Return an explanation of the query, often useful for optimization and
     * debugging
     *
     * @return array - Returns an explanation of the query.
     */
    public function explain()
    {
        $query = [
            '$query' => $this->getQuery(),
            '$explain' => true
        ];

        $response = $this->protocol->opQuery(
            $this->fcn,
            $query,
            $this->querySkip,
            $this->calculateRequestLimit(),
            0, //no flags
            MongoCursor::$timeout,
            $this->fields
        );

        return $response['result'][0];
    }

    /**
     * Sets the fields for a query
     *
     * @param array $fields - Fields to return (or not return).
     *
     * @return MongoCursor - Returns this cursor.
     */
    public function fields(array $fields)
    {
        $this->fields = $fields;

        return $this;
    }

    /**
     * Limits the number of results returned
     *
     * @param int $num - The number of results to return.
     *
     * @return MongoCursor - Returns this cursor.
     */
    public function limit($num)
    {
       $this->queryLimit = $num;

       return $this;
    }

    /**
     * Skips a number of results
     *
     * @param int $num - The number of results to skip.
     *
     * @return MongoCursor - Returns this cursor.
     */
    public function skip($num)
    {
        $this->querySkip = $num;

        return $this;
    }

    /**
     * Limits the number of elements returned in one batch.
     *
     * @param int $batchSize - The number of results to return per batch.
     *   Each batch requires a round-trip to the server.
     *
     * @return MongoCursor - Returns this cursor.
     */
    public function batchSize($batchSize)
    {
        $this->batchSize = $batchSize;

        return $this;
    }

    /**
     * Gets the query, fields, limit, and skip for this cursor
     *
     * @return array - Returns the namespace, limit, skip, query, and
     *   fields for this cursor.
     */
    public function info()
    {
        $info = [
            'ns' => $this->fcn,
            'limit' => $this->queryLimit,
            'skip' => $this->querySkip,
            'query' => $this->query['$query'],
            'fields' => $this->fields,
            'started_iterating' => $this->fetching
        ];

        //TODO: missing opReplay information
        return $info;
    }

    /**
     * Counts the number of results for this query
     *
     * @param bool $foundOnly -
     *
     * @return int - The number of documents returned by this cursor's
     *   query.
     */
    public function count($foundOnly = false)
    {
        $this->doQuery();

        if ($foundOnly) {
            return $this->countLocalData();
        }

        return $this->countQuerying();
    }

    private function countQuerying()
    {
        $ns = explode('.', $this->fcn, 2);

        $query = [
            'count' => $ns[1],
            'query' => $this->query['$query']
        ];

        $response = $this->protocol->opQuery(
            $ns[0] . '.$cmd', 
            $query, 0, -1, 0,
            $this->queryTimeout
        );

        return (int) $response['result'][0]['n'];
    }

    private function countLocalData()
    {
        return iterator_count($this);
    }

    /**
     * Execute the query.
     *
     * @return void - NULL.
     */
    protected function doQuery()
    {
        if (!$this->fetching) {
            $this->fetchDocuments();
        }
    }

    private function fetchDocuments()
    {
        $this->fetching = true;
        $response = $this->protocol->opQuery(
            $this->fcn,
            $this->getQuery(),
            $this->querySkip,
            $this->calculateRequestLimit(),
            0, //no flags
            $this->queryTimeout,
            $this->fields
        );

        $this->cursorId = $response['cursorId'];
        $this->setDocuments($response);
    }

    private function getQuery()
    {
        if (isset($this->query['$query']) && count($this->query) == 1) {
            return $this->query['$query'];
        }

        return $this->query;
    }

    private function calculateRequestLimit()
    {
        if ($this->queryLimit < 0) {
            return $this->queryLimit;
        } else if ($this->batchSize < 0) {
            return $this->batchSize;
        }

        if ($this->queryLimit > $this->batchSize) {
            return $this->batchSize;
        } else {
            return $this->queryLimit;
        }

        if ($this->batchSize && (!$limitAt || $this->batchSize <= $limitAt)) {
            return $this->batchSize;
        } else if ($limitAt && (!$limitAt || $this->batchSize > $limitAt)) {
            return $limitAt;
        }

        return 0;
    }

    private function fetchMoreDocumentsIfNeeded()
    {
        if (isset($this->documents[$this->currKey+1])) {
            return;
        }

        if ($this->cursorId) {
            $this->fetchMoreDocuments();
        } else {
            $this->end = true;
        }
    }
    
    private function fetchMoreDocuments()
    {
        $limit = $this->calculateNextRequestLimit();    
        if ($this->end) {
            return;
        }

        $response = $this->protocol->opGetMore(
            $this->fcn, 
            $limit, 
            $this->cursorId,
            $this->queryTimeout
        );

        $this->setDocuments($response);
    }

    private function calculateNextRequestLimit()
    {
        $current = count($this->documents);
        if ($this->queryLimit && $current >= $this->queryLimit) {
            $this->end = true;
            return 0;
        }

        if ($this->queryLimit >= $current) {
            $remaining = $this->queryLimit - $current;
        } else {
            $remaining = $this->queryLimit;
        }

        if ($remaining > $this->batchSize) {
            return $this->batchSize;
        }

        return $remaining;
    }

    private function setDocuments(array $response)
    {
        if (0 === $response['count']) {
            $this->end = true;
        }

        $this->documents = array_merge($this->documents, $response['result']);
    }

    /**
     * Adds a top-level key/value pair to a query
     *
     * @param string $key   - Fieldname to add.
     * @param mixed  $value - Value to add.
     *
     * @return MongoCursor - Returns this cursor.
     */
    public function addOption($key, $value)
    {
        throw new Exception('Not Implemented');
    }

    /**
     * Sets whether this cursor will wait for a while for a tailable cursor to
     * return more data
     *
     * @param bool $wait - If the cursor should wait for more data to
     *   become available.
     *
     * @return MongoCursor - Returns this cursor.
     */
    public function awaitData($wait = true)
    {
        throw new Exception('Not Implemented');
    }

    /**
     * Checks if there are documents that have not been sent yet from the
     * database for this cursor
     *
     * @return bool - Returns if there are more results that have not been
     *   sent to the client, yet.
     */
    public function dead()
    {
        throw new Exception('Not Implemented');
    }

    /**
     * Return the next object to which this cursor points, and advance the
     * cursor
     *
     * @return array - Returns the next object.
     */
    public function getNext()
    {        
        $record = $this->current();
        $this->next();

        return $record;
    }

    /**
     * Checks if there are any more elements in this cursor
     *
     * @return bool - Returns if there is another element.
     */
    public function hasNext()
    {
        $this->doQuery();
        $this->fetchMoreDocumentsIfNeeded();

        return isset($this->documents[$this->currKey+1]);
    }

    /**
     * Get the read preference for this query
     *
     * @return array -
     */
    public function getReadPreference()
    {
        throw new Exception('Not Implemented');
    }

    /**
     * Sets whether this cursor will timeout
     *
     * @param bool $liveForever - If the cursor should be immortal.
     *
     * @return MongoCursor - Returns this cursor.
     */
    public function immortal($liveForever = true)
    {
        throw new Exception('Not Implemented');
    }

    /**
     * If this query should fetch partial results from  if a shard is down
     *
     * @param bool $okay - If receiving partial results is okay.
     *
     * @return MongoCursor - Returns this cursor.
     */
    public function partial($okay = true)
    {
        throw new Exception('Not Implemented');
    }

    /**
     * Sets arbitrary flags in case there is no method available the specific
     * flag
     *
     * @param int $flag - Which flag to set. You can not set flag 6
     *   (EXHAUST) as the driver does not know how to handle them. You will
     *   get a warning if you try to use this flag. For available flags,
     *   please refer to the wire protocol documentation.
     * @param bool $set - Whether the flag should be set (TRUE) or unset
     *   (FALSE).
     *
     * @return MongoCursor - Returns this cursor.
     */
    public function setFlag($flag, $set = true)
    {
        throw new Exception('Not Implemented');
    }

    /**
     * Set the read preference for this query
     *
     * @param string $readPreference -
     * @param array  $tags           -
     *
     * @return MongoCursor - Returns this cursor.
     */
    public function setReadPreference($readPreference, array $tags = [])
    {
        throw new Exception('Not Implemented');
    }

    /**
     * Sets whether this query can be done on a secondary
     *
     * @param bool $okay - If it is okay to query the secondary.
     *
     * @return MongoCursor - Returns this cursor.
     */
    public function slaveOkay($okay = true)
    {
        throw new Exception('Not Implemented');
    }


    /**
     * Sets whether this cursor will be left open after fetching the last
     * results
     *
     * @param bool $tail - If the cursor should be tailable.
     *
     * @return MongoCursor - Returns this cursor.
     */
    public function tailable($tail = true)
    {
        throw new Exception('Not Implemented');
    }

    /**
     * Sets a client-side timeout for this query
     *
     * @param int $ms -
     *
     * @return MongoCursor - This cursor.
     */
    public function timeout($ms)
    {
        $this->queryTimeout = $ms;

        return $this;
    }

    /**
     * Returns the current element
     *
     * @return array - The current result as an associative array.
     */
    public function current()
    {
        $this->doQuery();
        if (!isset($this->documents[$this->currKey])) {
            return null;
        }

        return $this->documents[$this->currKey];
    }

    /**
     * Advances the cursor to the next result
     *
     * @return void - NULL.
     */
    public function next()
    {
        $this->doQuery();
        $this->fetchMoreDocumentsIfNeeded();

        $this->currKey++;
    }

    /**
     * Returns the current results _id
     *
     * @return string - The current results _id as a string.
     */
    public function key()
    {
        $record = $this->current();

        if (!isset($record['_id'])) {
            return $this->currKey;
        }

        return (string) $record['_id'];
    }

    /**
     * Checks if the cursor is reading a valid result.
     *
     * @return bool - If the current result is not null.
     */
    public function valid()
    {
        $this->doQuery();

        return !$this->end;
    }

    /**
     * Returns the cursor to the beginning of the result set
     *
     * @return void - NULL.
     */
    public function rewind()
    {
        $this->currKey = 0;
        $this->end = false;
    }

    /**
     * INTERNAL: Gets the cursor id (not part of the original driver)
     *
     * @return int|null
     */
    public function _getCursorId()
    {
        return $this->cursorId;
    }
}



/**
 * Caused by accessing a cursor incorrectly or a error receiving a reply.
 */
class MongoCursorException extends Exception
{
    /**
     * The hostname of the server that encountered the error
     *
     * @return string - Returns the hostname, or NULL if the hostname is
     *   unknown.
     */
    public function getHost()
    {
        throw new Exception('Not Implemented');
    }
}


/**
 * Caused by a query timing out. You can set the length of time to wait before
 * this exception is thrown by calling MongoCursor::timeout() on the cursor or
 * setting MongoCursor::$timeout.
 */
class MongoCursorTimeoutException extends Exception
{
}




class MongoDB
{
    const NAMESPACES_COLLECTION = 'system.namespaces';
    const INDEX_COLLECTION = 'system.indexes';

    /**
     * @var string
     */
    private $name;

    /**
     * @var MongoClient
     */
    private $client;

    /**
     * @var Protocol
     */
    private $protocol;

    /**
     * @var array
     */
    private $collections = [];

    /**
     * Creates a new database
     *
     * @param mongoclient $conn - Database connection.
     * @param string $name - Database name.
     *
     * @return  - Returns the database.
     */
    public function __construct(MongoClient $client, $name)
    {
        $this->name = $name;
        $this->client = $client;
        $this->protocol = $client->_getProtocol();
    }

    /**
     * Gets a collection
     *
     * @param string $name - The name of the collection.
     *
     * @return MongoCollection - Returns the collection.
     */
    public function __get($name)
    {
        return $this->selectCollection($name);
    }

    /**
     * @return MongoClient
     */
    public function _getClient()
    {
        return $this->client;
    }

    /**
     * Gets a collection
     *
     * @param string $name - The collection name.
     *
     * @return MongoCollection - Returns a new collection object.
     */
    public function selectCollection($name)
    {
        if (!isset($this->collections[$name])) {
            $this->collections[$name] = new MongoCollection($this, $name);
        }
        return $this->collections[$name];
    }


    public function _getFullCollectionName($collectionName)
    {
        return $this->name . '.' . $collectionName;
    }

    /**
     * Drops this database
     *
     * @return array - Returns the database response.
     */
    public function drop()
    {
        $cmd = ['dropDatabase' => 1];
        
        return $this->command($cmd);
    }

    /**
     * Execute a database command
     *
     * @param array $command - The query to send.
     * @param array $options - This parameter is an associative array of
     *   the form array("optionname" => boolean, ...). 
     *
     * @return array - Returns database response. 
     */
    public function command(array $cmd, array $options = [])
    {
        $timeout = MongoCursor::$timeout;
        if (!empty($options['timeout'])) {
            $timeout = $options['timeout'];
        }

        $response = $this->protocol->opQuery(
            "{$this->name}.\$cmd", 
            $cmd, 
            0, -1, 0,
            $timeout
        );

        return $response['result'][0];
    }

    /**
     * Get all collections from this database
     *
     * @param bool $includeSystemCollections -
     *
     * @return array - Returns the names of the all the collections in the
     *   database as an array.
     */
    public function getCollectionNames($includeSystemCollections = false)
    {
        $collections = [];
        $namespaces = $this->selectCollection(self::NAMESPACES_COLLECTION);
        foreach ($namespaces->find() as $collection) {
            if (
                !$includeSystemCollections && 
                $this->isSystemCollection($collection['name'])
            ) {
                continue;
            }

            if ($this->isAnIndexCollection($collection['name'])) {
                continue;
            }

            $collections[] = $this->getCollectionName($collection['name']);
        }

        return $collections;
    }

    /**
     * Gets an array of all MongoCollections for this database
     *
     * @param bool $includeSystemCollections -
     *
     * @return array - Returns an array of MongoCollection objects.
     */
    public function listCollections($includeSystemCollections = false)
    {
        $collections = [];
        $names = $this->getCollectionNames($includeSystemCollections);
        foreach ($names as $name) {
            $collections[] = $this->selectCollection($name);
        }

        return $collections;
    }

    private function isAnIndexCollection($namespace)
    {
        return !strpos($namespace, '$') === false;
    }


    private function isSystemCollection($namespace)
    {
        return !strpos($namespace, '.system.') === false;
    }

    private function getCollectionName($namespace)
    {
        $dot = strpos($namespace, '.');

        return substr($namespace, $dot + 1);
    }

    public function getIndexesCollection()
    {
        return $this->selectCollection(self::INDEX_COLLECTION);
    }

    /**
     * Fetches toolkit for dealing with files stored in this database
     *
     * @param string $prefix - The prefix for the files and chunks
     *   collections.
     *
     * @return MongoGridFS - Returns a new gridfs object for this database.
     */
    public function getGridFS($prefix = 'fs')
    {
        return new MongoGridFS($this, $prefix);
    }

    /**
     * Log in to this database
     *
     * @param string $username - The username.
     * @param string $password - The password (in plaintext).
     *
     * @return array - Returns database response. If the login was
     *   successful, it will return    If something went wrong, it will
     *   return    ("auth fails" could be another message, depending on
     *   database version and what when wrong).
     */
    public function authenticate($username, $password)
    {
        throw new Exception('Not Implemented');
    }

    /**
     * Creates a collection
     *
     * @param string $name - The name of the collection.
     * @param array $options - An array containing options for the
     *   collections. Each option is its own element in the options array,
     *   with the option name listed below being the key of the element. The
     *   supported options depend on the MongoDB server version. At the
     *   moment, the following options are supported:      capped    If the
     *   collection should be a fixed size.      size    If the collection is
     *   fixed size, its size in bytes.      max    If the collection is
     *   fixed size, the maximum number of elements to store in the
     *   collection.      autoIndexId    If capped is TRUE you can specify
     *   FALSE to disable the automatic index created on the _id field.
     *   Before MongoDB 2.2, the default value for autoIndexId was FALSE.
     *
     * @return MongoCollection - Returns a collection object representing
     *   the new collection.
     */
    public function createCollection($name, array $options = [])
    {
        $options['create'] = $name;

        return $this->command($options);
    }

    /**
     * Creates a database reference
     *
     * @param string $collection - The collection to which the database
     *   reference will point.
     * @param mixed $documentOrId - If an array or object is given, its
     *   _id field will be used as the reference ID. If a MongoId or scalar
     *   is given, it will be used as the reference ID.
     *
     * @return array - Returns a database reference array.   If an array
     *   without an _id field was provided as the document_or_id parameter,
     *   NULL will be returned.
     */
    public function createDBRef($collection, $documentOrId)
    {
        throw new Exception('Not Implemented');
    }

    /**
     * Drops a collection [deprecated]
     *
     * @param mixed $coll - MongoCollection or name of collection to drop.
     *
     * @return array - Returns the database response.
     */
    public function dropCollection($coll)
    {
        $collection = $this->selectCollection($coll);
        if (!$collection) {
            return;
        }

        return $collection->drop();
    }

    /**
     * Runs JavaScript code on the database server.
     *
     * @param mixed $code - MongoCode or string to execute.
     * @param array $args - Arguments to be passed to code.
     *
     * @return array - Returns the result of the evaluation.
     */
    public function execute($code, array $args = [])
    {
        return $this->command(array('$eval' => $code, 'args' => $args));
    }

    /**
     * Creates a database error
     *
     * @return bool - Returns the database response.
     */
    public function forceError()
    {
        throw new Exception('Not Implemented');
    }

    /**
     * Fetches the document pointed to by a database reference
     *
     * @param array $ref - A database reference.
     *
     * @return array - Returns the document pointed to by the reference.
     */
    public function getDBRef(array $ref)
    {
        throw new Exception('Not Implemented');
    }

    /**
     * Gets this databases profiling level
     *
     * @return int - Returns the profiling level.
     */
    public function getProfilingLevel()
    {
        throw new Exception('Not Implemented');
    }

    /**
     * Get the read preference for this database
     *
     * @return array -
     */
    public function getReadPreference()
    {
        throw new Exception('Not Implemented');
    }

    /**
     * Get slaveOkay setting for this database
     *
     * @return bool - Returns the value of slaveOkay for this instance.
     */
    public function getSlaveOkay()
    {
        throw new Exception('Not Implemented');
    }

    /**
     * Check if there was an error on the most recent db operation performed
     *
     * @return array - Returns the error, if there was one.
     */
    public function lastError()
    {
        throw new Exception('Not Implemented');
    }

    /**
     * Checks for the last error thrown during a database operation
     *
     * @return array - Returns the error and the number of operations ago
     *   it occurred.
     */
    public function prevError()
    {
        throw new Exception('Not Implemented');
    }

    /**
     * Repairs and compacts this database
     *
     * @param bool $preserve_cloned_files - If cloned files should be kept
     *   if the repair fails.
     * @param bool $backup_original_files - If original files should be
     *   backed up.
     *
     * @return array - Returns db response.
     */
    public function repair($preserveClonedFiles = false, $backupOriginalFiles = false)
    {
        throw new Exception('Not Implemented');
    }

    /**
     * Clears any flagged errors on the database
     *
     * @return array - Returns the database response.
     */
    public function resetError()
    {
        throw new Exception('Not Implemented');
    }

    /**
     * Sets this databases profiling level
     *
     * @param int $level - Profiling level.
     *
     * @return int - Returns the previous profiling level.
     */
    public function setProfilingLevel($level)
    {
        throw new Exception('Not Implemented');
    }

    /**
     * Set the read preference for this database
     *
     * @param string $readPreference -
     * @param array $tags -
     *
     * @return bool -
     */
    public function setReadPreference($readPreference, array $tags = null)
    {
        throw new Exception('Not Implemented');
    }

    /**
     * Change slaveOkay setting for this database
     *
     * @param bool $ok - If reads should be sent to secondary members of a
     *   replica set for all possible queries using this MongoDB instance.
     *
     * @return bool - Returns the former value of slaveOkay for this
     *   instance.
     */
    public function setSgetGridFSlaveOkay($ok = true)
    {
        throw new Exception('Not Implemented');
    }

    /**
     * The name of this database
     *
     * @return string - Returns this databases name.
     */
    public function __toString()
    {
        return $this->name;
    }
}



/**
 * This class can be used to create lightweight links between objects in
 * different collections. 
 */
class MongoDBRef {

    private function __construct($collection, $id, $db = null)
    {
        $this->{'$ref'} = $collection;
        $this->{'$id'} = $id;
        if ($db) {
            $this->{'$db'} = $db;
        }
    }

    /**
     * Creates a new database reference
     *
     * @param string $collection - Collection name (without the database
     *   name).
     * @param mixed $id - The _id field of the object to which to link.
     * @param string $database - Database name.
     *
     * @return array - Returns the reference.
     */
    public static function create($collection, $id, $database = null)
    {
        return new MongoDBRef($collection, $id, $database);
    }

    /**
     * Fetches the object pointed to by a reference
     *
     * @param mongodb $db - Database to use.
     * @param array $ref - Reference to fetch.
     *
     * @return array - Returns the document to which the reference refers
     *   or NULL if the document does not exist (the reference is broken).
     */
    public static function get(MongoDB $db, $ref)
    {
        $ref = (array)$ref;
        if (!isset($ref['$id']) || !isset($ref['$ref'])) {
            return;
        }
        $ns = $ref['$ref'];
        $id = $ref['$id'];

        $refdb = null;
        if (isset($ref['$db'])) {
            $refdb = $ref['$db'];
        }

        if (!is_string($ns)) {
            throw new MongoException('MongoDBRef::get: $ref field must be a string', 10);
        }
        if (isset($refdb)) {
            if (!is_string($refdb)) {
                throw new MongoException('MongoDBRef::get: $db field of $ref must be a string', 11);
            }
            if ($refdb != (string)$db) {
                $db = $db->_getClient()->$refdb;
            }
        }
        $collection = new MongoCollection($db, $ns);
        $query = ['_id' => $id];
        return $collection->findOne($query);
    }

    /**
     * Checks if an array is a database reference
     *
     * @param mixed $ref - Array or object to check.
     *
     * @return bool -
     */
    public static function isRef($ref)
    {
        if (is_array($ref)) {
            if (isset($ref['$id']) && isset($ref['$ref'])) {
                return true;
            }
        } elseif (is_object($ref)) {
            if (isset($ref->{'$ref'}) && isset($ref->{'$id'})) {
                return true;
            }
        }
        return false;
    }
}




/**
 * Represent date objects for the database. This class should be used to save
 * dates to the database and to query for dates. 
 */
class MongoDate
{
    /**
     * @var int
     */
    public $sec ;

    /**
     * @var int
     */
    public $usec ;

    /**
     * Creates a new date.
     *
     * @param int $sec - Number of seconds since January 1st, 1970.
     * @param int $usec - Microseconds. Please be aware though that
     *   MongoDB's resolution is milliseconds and not microseconds, which
     *   means this value will be truncated to millisecond resolution.
     *
     * @return  - Returns this new date.
     */
    public function __construct($sec = 0, $usec = 0) {
        if (func_num_args() == 0) {
            $time = microtime(true);
            $sec = floor($time);
            $usec = ($time - $sec) * 1000000.0;
        }
        $this->sec = $sec;
        $this->usec = (int)floor(($usec / 1000)) * 1000;
    }

    /**
     * Returns a string representation of this date
     *
     * @return string - This date.
     */
    public function __toString() {
        return (string) $this->sec . ' ' . $this->usec;
    }

    /**
     * returns date in milliseconds since Unix epoch
     *
     * @return int
     */
    public function getMs() {
        return $this->sec*1000 + (int)floor($this->usec/1000);
    }

    /**
     * Creates MongoDate from milliseconds
     *
     * @param int milliseconds since Unix epoch
     */
    public static function createFromMs($val) {
        $usec = (int)(((($val * 1000) % 1000000) + 1000000) % 1000000);
        $sec = (int)(($val / 1000) - ($val < 0 && $usec));
        return new MongoDate($sec, $usec);
    }
}



/**
 * Default Mongo exception.
 */
class MongoException extends Exception
{
    const NOT_CONNECTED = 0;
    const SAVE_EMPTY_KEY = 1;
    const KEY_CONTAINS_DOT = 2;
    const INSERT_TOO_LARGE = 3;
    const NO_ELEMENTS = 4;
    const DOC_TOO_BIG = 5;
    const NO_DOC_SUPPLIED = 6;
    const WRONG_GROUP_TYPE = 7;
    const KEY_NOT_STRING = 8;
    const INVALID_REGEX = 9;
    const REF_NOT_STRING = 10;
    const DB_NOT_STRING = 11;
    const NON_UTF_STRING = 12;
    const MUTEX_ERROR = 13;
    const INDEX_TOO_LONG = 14;
} 



/**
 * Utilities for storing and retrieving files from the database.   
 */
class MongoGridFS extends MongoCollection
{
    const DEFAULT_CHUNK_SIZE = 262144; //256k

    /**
     * @var MongoCollection
     */
    public $chunks;

    /**
     * @var MongoCollection
     */
    private $files;

    /**
     * @var MongoDB
     */
    private $db;

    /**
     * @var string
     */
    protected $filesName;

    /**
     * @var string
     */
    protected $chunksName;

    /**
     * Creates new file collections
     *
     * @param mongodb $db - Database.
     * @param string $prefix -
     * @param mixed $chunks -
     */
    public function __construct(MongoDB $db, $prefix = 'fs', $chunks = 'fs')
    {
        $this->db = $db;
        $thisName = $prefix . '.files';
        $this->chunksName = $prefix . '.chunks';

        $this->chunks = $db->selectCollection($this->chunksName);

        parent::__construct($db, $thisName);
    }

    /**
     * Delete a file from the database
     *
     * @param mixed $id - _id of the file to remove.
     * @return bool - Returns if the remove was successfully sent to the database.
     */
    public function delete($id)
    {
        return $this->remove(['_id' => $id]); 
    }

    /**
     * Drops the files and chunks collections
     *
     * @return array - The database response.
     */
    public function drop()
    {
        $this->chunks->drop();
        parent::drop();
    }

    /**
     * Queries for files
     *
     * @param array $query - The query.
     * @param array $fields - Fields to return.
     * @return MongoGridFSCursor - A MongoGridFSCursor.
     */
    public function find(array $query = [], array $fields = [])
    {
        return new MongoGridFSCursor(
            $this,
            $this->db->_getClient(), 
            $this->__toString(),
            $query,
            $fields
        );
    }

    /**
     * Returns a single file matching the criteria
     *
     * @param mixed $query - The filename or criteria for which to search.
     * @param mixed $fields - Fields to return.
     * @return MongoGridFSFile - Returns a MongoGridFSFile or NULL.
     */
    public function findOne($query = [], array $fields = [])
    {
        if (is_string($query)) {
            $query = ['filename' => $query];
        }

        $cur = $this->find($query, $fields)->limit(1);

        return $cur->current();
    }

    /**
     * Retrieve a file from the database
     *
     * @param mixed $id - _id of the file to find.
     * @return MongoGridFSFile - Returns the file, if found, or NULL.
     */
    public function get($id)
    {
        return $this->findOne(['_id' => $id]);
    }

    /**
     * Stores a file in the database
     *
     * @param string $filename - Name of the file to store.
     * @param array $metadata - Other metadata fields to include in the document.
     * @return mixed -
     */
    public function put($filename, array $metadata = [])
    {
        return $this->storeFile($filename, $metadata);
    }

    /**
     * Removes files from the collections
     *
     * @param array $criteria -
     * @param array $options - Options for the remove. Valid options are:
     * @return bool - Returns if the removal was successfully sent to the database.
     */
    public function remove(array $criteria = [],  array $options = [])
    {
        //TODO: implement $options
        $files = parent::find($criteria, ['_id' => 1]);
        $ids = [];
        foreach ($files as $record) {
            $ids[] = $record['_id'];
        }

        if (!$ids) {
            return false;
        }

        $this->chunks->remove(['files_id' => [
            '$in' => $ids
        ]]);

        return parent::remove(['_id' => [
            '$in' => $ids
        ]], $options);
    }

    /**
     * Stores a string of bytes in the database
     *
     * @param string $bytes - String of bytes to store.
     * @param array $metadata - Other metadata fields to include in the document.
     * @param array $options - Options for the store.
     *
     * @return mixed -
     */
    public function storeBytes($bytes, array $metadata = [], array $options = [])
    {
        $chunkSize = self::DEFAULT_CHUNK_SIZE;
        if (isset($metadata['chunkSize'])) {
            $chunkSize = $metadata['chunkSize'];
        }

        $file = $this->insertFileFromBytes($bytes, $metadata, $chunkSize);
        $this->insertChunksFromBytes($bytes, $file['_id'], $chunkSize);

        return $file['_id'];
    }

    private function insertFileFromBytes($bytes, array $metadata, $chunkSize)
    {
        $record = [
            'uploadDate' => new MongoDate(),
            'chunkSize' => $chunkSize,
            'length' => mb_strlen($bytes, '8bit'),
            'md5' => md5($bytes)
        ];

        $record = array_merge($metadata, $record);
        $this->insert($record);

        return $record;
    }

    private function insertChunksFromBytes($bytes, $id, $chunkSize)
    {
        $length = mb_strlen($bytes, '8bit');
        $offset = 0;
        $n = 0;

        while($offset < $length) {
            $data = mb_substr($bytes, $offset, $chunkSize, '8bit');
            $this->insertChunk($id, $data, $n++);

            $offset += $chunkSize;
        }
    }

    /**
     * Stores a file in the database
     *
     * @param string $filename - Name of the file to store.
     * @param array $metadata - Other metadata fields to include in the document.
     * @param array $options - Options for the store.
     * @return mixed -
     */
    public function storeFile($filename, array $metadata = [], array $options = [])
    {
        $this->throwExceptionIfFilenameNotExists($filename);

        $chunkSize = self::DEFAULT_CHUNK_SIZE;
        if (isset($metadata['chunkSize'])) {
            $chunkSize = $metadata['chunkSize'];
        }

        $file = $this->insertFileFromFilename($filename, $metadata, $chunkSize);
        $this->insertChunksFromFilename($filename, $file['_id'], $chunkSize);

        return $file['_id'];
    }

    private function throwExceptionIfFilenameNotExists($filename)
    {
        if (!file_exists($filename)) {
            throw new MongoException(sprintf(
                'error setting up file: %s', 
                $filename
            ));
        }
    }
    
    private function insertFileFromFilename($filename, array $metadata, $chunkSize)
    {
        $record = [
            'filename' => $filename,
            'uploadDate' => new MongoDate(),
            'chunkSize' => $chunkSize,
            'length' => filesize($filename),
            'md5' => md5_file($filename)
        ];

        if (isset($metadata['filename'])) {
            $record['filename'] = $metadata['filename'];
        }

        $record = array_merge($metadata, $record);
        $this->insert($record);

        return $record;
    }

    private function insertChunksFromFilename($filename, MongoId $id, $chunkSize)
    {
        $handle = fopen($filename, 'r');

        $n = 0;
        while (!feof($handle)) {
            $data = stream_get_contents($handle, $chunkSize);
            $this->insertChunk($id, $data, $n++);
        }

        fclose($handle);
    }

    private function insertChunk($filesId, $data, $n)
    {
        $record = [
            'files_id' => $filesId,
            'data' => new MongoBinData($data),
            'n' => $n
        ];

        return $this->chunks->insert($record);
    }

    /**
     * Stores an uploaded file in the database
     *
     * @param string $name - The name of the uploaded file to store. This
     *   should correspond to the file field's name attribute in the HTML
     *   form.
     * @param array $metadata - Other metadata fields to include in the
     *   file document.    The filename index will be populated with the
     *   filename used.
     * @return mixed -
     */
    public function storeUpload($name, array $metadata  = [])
    {
        $this->throwExceptionIfMissingUpload($name);
        $this->throwExceptionIfMissingTmpName($name);

        $uploaded = $_FILES[$name];
        $uploaded['tmp_name'] = (array) $uploaded['tmp_name'];
        $uploaded['name'] = (array) $uploaded['name'];

        $results = [];
        foreach ($uploaded['tmp_name'] as $key => $file) {
            $metadata['filename'] = $uploaded['name'][$key];
            $results[] = $this->storeFile($file, $metadata);
        }

        return $results;
    }

    private function throwExceptionIfMissingUpload($name)
    {
        if (isset($_FILES[$name])) {
            return;
        }

        throw new MongoGridFSException(sprintf(
            'could not find uploaded file %s',
            $name
        ), 11);
    }

    private function throwExceptionIfMissingTmpName($name)
    {
        if (isset($_FILES[$name]['tmp_name']) && (
            is_array($_FILES[$name]['tmp_name']) ||
            is_string($_FILES[$name]['tmp_name'])
        )) {
            return;
        }

        throw new MongoGridFSException(
            'tmp_name was not a string or an array', 
            13
        );
    }
}


/**
 * Cursor for database file results.
 */
class MongoGridFSCursor extends MongoCursor
{
    /**
     * @var MongoClient
     */
    private $cursor;
    
    /**
     * @var MongoGridFS
     */
    protected $gridfs;

    /**
     * Create a new cursor
     *
     * @param mongogridfs $gridfs - Related GridFS collection.
     * @param resource $connection - Database connection.
     * @param string $ns - Full name of database and collection.
     * @param array $query - Database query.
     * @param array $fields - Fields to return.
     *
     * @return  - Returns the new cursor.
     */
    public function __construct(
        MongoGridFS $gridfs, 
        MongoClient $connection, 
        $ns, 
        array $query, 
        array $fields
    )
    {
        $this->gridfs = $gridfs;
        
        parent::__construct($connection, $ns, $query, $fields);
    }

    /**
     * Returns the current file
     *
     * @return MongoGridFSFile - The current file.
     */
    public function current()
    {
        $current = parent::current();
        if (!$current) {
            return null;
        }

        return new MongoGridFSFile($this->gridfs, $current);
    }

    /**
     * Return the next file to which this cursor points, and advance the cursor
     *
     * @return MongoGridFSFile - Returns the next file.
     */
    public function getNext()
    {
        parent::getNext();

        return $this->current();
    }

    /**
     * Returns the current results filename
     *
     * @return string - The current results _id as a string.
     */
    public function key()
    {
        return parent::key();
    }

    public function limit($limit)
    {
        parent::limit($limit);

        return $this;
    }

    public function sort(array $fields)
    {
        parent::sort($fields);

        return $this;
    }

    public function skip($skip)
    {
        parent::skip($skip);

        return $this;
    }

    public function count($foundOnly = false)
    {
        return parent::count($foundOnly);
    }
}


/**
 * Thrown when there are errors reading or writing files to or from the
 * database.
 */
class MongoGridFSException extends Exception
{
}



/**
 * A database file object.
 */
class MongoGridFSFile
{
    /**
     * @var array
     */
    public $file;
    
    /**
     * @var MongoGridFS
     */
    protected $gridfs;

    /**
     * Create a new GridFS file
     *
     * @param mongogridfs $gridfs - The parent MongoGridFS instance.
     * @param array $file - A file from the database.
     *
     * @return  - Returns a new MongoGridFSFile.
     */
    public function __construct(MongoGridFS $gridfs, array $file)
    {
        $this->gridfs = $gridfs;
        $this->file = $file;
    }

    /**
     * Returns this files filename
     *
     * @return string - Returns the filename.
     */
    public function getFilename()
    {
        return $this->file['filename'];
    }

    /**
     * Returns this files size
     *
     * @return int - Returns this file's size
     */
    public function getSize()
    {
        return $this->file['length'];
    }

    /**
     * Returns this files contents as a string of bytes
     *
     * @return string - Returns a string of the bytes in the file.
     */
    public function getBytes()
    {
        $this->trowExceptionIfInvalidLength();

        $bytes = '';

        $query = ['files_id' => $this->file['_id']];
        $sort = ['n' => 1];

        $chunks = $this->gridfs->chunks->find($query)->sort($sort);
        foreach ($chunks as $chunk) {
            $bytes .= $chunk['data']->bin;
        };

        return $bytes;
    }

    private function trowExceptionIfInvalidLength()
    {
        if (!isset($this->file['length'])) {
            throw new MongoException('couldn\'t find file size', 14);
            
        }
    }

    /**
     * Returns a resource that can be used to read the stored file
     *
     * @return stream - Returns a resource that can be used to read the
     *   file with
     */
    public function getResource()
    {
        throw new Exception('Not implemented');
    }

    /**
     * Writes this file to the filesystem
     *
     * @param string $filename - The location to which to write the file.
     *   If none is given, the stored filename will be used.
     *
     * @return int - Returns the number of bytes written.
     */
    public function write($filename = null)
    {
        if (!$filename) {
            $this->trowExceptionIfInvalidFilename();
            $filename = $this->file['filename'];
        }

        $bytes = $this->getBytes();
        
        return file_put_contents($filename, $bytes);
    }

    private function trowExceptionIfInvalidFilename()
    {
        if (!isset($this->file['filename'])) {
            throw new MongoException('Cannot find filename', 15);
        }
    }
}


/**
 * A unique identifier created for database objects. If an object is inserted
 * into the database without an _id field, an _id field will be added to it
 * with a MongoId instance as its value.
 */
class MongoId
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var int
     */
    private $timestamp;

    /**
     * @var string
     */
    private $hostname;

    /**
     * @var int
     */
    private $pid;

    /**
     * @var int
     */
    private $inc;

    /**
     * @var int
     */
    static private $refInc = null;

    /**
     * Creates a new id
     *
     * @param string $id - A string to use as the id. Must be 24
     *   hexidecimal characters. If an invalid string is passed to this
     *   constructor, the constructor will ignore it and create a new id
     *   value.
     *
     * @return  - Returns a new id.
     */
    public function __construct($id = null)
    {
        $this->hostname = self::getHostname();
        if (null === $id) {
            $id = $this->generateId();
        } else if (self::isValid($id)) {
            $this->disassembleId($id);
        } else {
            throw new MongoException('Invalid object ID', 19);
        }

        $this->id = $id;
        $this->{'$id'} = $id;
    }

    private function generateId()
    {
        if (null === self::$refInc) {
           self::$refInc = (int) mt_rand(0, pow(2, 24));
        }

        $this->timestamp = time();
        $this->inc = self::$refInc++;
        $this->pid = getmypid();

        if ($this->pid > 32768) {
            $this->pid = $this->pid - 32768;
        }

        return $this->assembleId();
    }

    private function assembleId()
    {
        $hash = unpack('a3hash', md5($this->hostname, true))['hash'];
        $i1 = ($this->inc) & 255;
        $i2 = ($this->inc >> 8) & 255;
        $i3 = ($this->inc >> 16) & 255;
        $binId = pack(
            'Na3vC3',
            $this->timestamp,
            $hash,
            $this->pid,
            $i3, $i2, $i1
        );

        return bin2hex($binId);
    }

    private function disassembleId($id)
    {
        $vars = unpack('Nts/C3m/vpid/C3i', hex2bin($id));
        $this->timestamp = $vars['ts'];
        $this->pid = $vars['pid'];
        $this->inc = $vars['i3'] | ($vars['i2'] << 8) | ($vars['i1'] << 16);
    }

    /**
     * Gets the hostname being used for this machine's ids
     *
     * @return string - Returns the hostname.
     */
    public static function getHostname()
    {
        return gethostname();
    }

    /**
     * Gets the number of seconds since the epoch that this id was created
     *
     * @return int - Returns the number of seconds since the epoch that
     *   this id was created. There are only four bytes of timestamp stored,
     *   so MongoDate is a better choice for storing exact or wide-ranging
     *   times.
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * Gets the process ID
     *
     * @return int - Returns the PID of the MongoId.
     */
    public function getPid()
    {
        return $this->pid;
    }

    /**
     * Gets the incremented value to create this id
     *
     * @return int - Returns the incremented value used to create this
     *   MongoId.
     */
    public function getInc()
    {
        return $this->inc;
    }

    /**
     * Check if a value is a valid ObjectId
     *
     * @param mixed $value - The value to check for validity.
     *
     * @return bool - Returns TRUE if value is a MongoId instance or a
     *   string consisting of exactly 24 hexadecimal characters; otherwise,
     *   FALSE is returned.
     */
    public static function isValid($id)
    {
        if (!is_string($id)) {
            return false;
        }

        return preg_match('/[0-9a-fA-F]{24}/', $id);
    }

    /**
     * Create a dummy MongoId
     *
     * @param array $props - Theoretically, an array of properties used to
     *   create the new id. However, as MongoId instances have no properties,
     *   this is not used.
     *
     * @return MongoId - A new id with the value
     *   "000000000000000000000000".
     */
    public static function __set_state($props)
    {
        $id = new self('000000000000000000000000');
        foreach($props as $propName => $value) {
            $id->{$propName} = $value;
        }
        $id->id = $id->assembleId();
        return $id;
    }

    /**
     * Returns a hexidecimal representation of this id
     *
     * @return string - This id.
     */
    public function __toString()
    {
        return (string)$this->id;
    }
}



/**
 * The class can be used to save 32-bit integers to the database on a 64-bit
 * system.
 */
class MongoInt32
{
    /**
     * @var string
     */
    public $value;

    /**
     * Creates a new 32-bit integer.
     *
     * @param string $value - A number.
     *
     * @return  - Returns a new integer.
     */
    public function __construct($value)
    {
        $this->value = (string) $value;
    }

    /**
     * Returns the string representation of this 32-bit integer.
     *
     * @return string - Returns the string representation of this integer.
     */
    public function __toString()
    {
        return (string) $this->value;
    }
}


/**
 * The class can be used to save 64-bit integers to the database on a 32-bit
 * system.
 */
class MongoInt64
{
    /**
     * @var string
     */
    public $value;

    /**
     * Creates a new 64-bit integer.
     *
     * @param string $value - A number.
     *
     * @return  - Returns a new integer.
     */
    public function __construct($value)
    {
        $this->value = (string) $value;
    }

    /**
     * Returns the string representation of this 64-bit integer.
     *
     * @return string - Returns the string representation of this integer.
     */
    public function __toString()
    {
        return (string) $this->value;
    }
}


/**
 * Logging can be used to get detailed information about what the driver is
 * doing. The logging mechanism as used by MongoLog emits all log messages as
 * a PHP notice.
 */
class MongoLog
{
    /* Constants */
    const NONE = 0;
    const ALL = 31;

    /* Level constants */
    const WARNING = 1;
    const INFO = 2;
    const FINE = 4;

    /* Module constants */
    const RS = 1;
    const POOL = 1; // This is not a typo, it's mapped to RS for backwards compatibility.
    const IO = 4;
    const SERVER = 8;
    const PARSE = 16;

    /**
     * @var int
     */
    private static $level;

    /**
     * @var int
     */
    private static $module;

    /**
     * @var callable
     */
    private static $callback;

    /**
     * Retrieve the previously set callback function name
     *
     * @return void - Returns the callback function name, or FALSE if not
     *   set yet.
     */
    public static function getCallback()
    {
        return self::$callback;
    }

    /**
     * Gets the log level
     *
     * @return int - Returns the current level.
     */
    public static function getLevel()
    {
        return self::$level; 
    }

    /**
     * Gets the modules currently being logged
     *
     * @return int - Returns the modules currently being logged.
     */
    public static function getModule()
    {
        return self::$module;
    }

    /**
     * Set a callback function to be called on events
     *
     * @param callable $logFunction - The function to be called on events.
     *
     * @return void -
     */
    public static function setCallback(callable $logFunction)
    {
        self::$callback = $logFunction;
    }

    /**
     * Sets logging level
     *
     * @param int $level - The levels you would like to log.
     *
     * @return void -
     */
    public static function setLevel($level)
    {
        self::$level = $level;
    }

    /**
     * Sets driver functionality to log
     *
     * @param int $module - The module(s) you would like to log.
     *
     * @return void -
     */
    public static function setModule($module)
    {
        self::$module = $module;
    }
}



/**
 * MongoMaxKey is a special type used by the database that evaluates to
 * greater than any other type. 
 */
class MongoMaxKey
{
}


/**
 * MongoMinKey is a special type used by the database that evaluates to less
 * than any other type.
 */
class MongoMinKey
{
}


/**
 * This class can be used to create regular expressions. Typically, these
 * expressions will be used to query the database and find matching strings.
 * More unusually, they can be saved to the database and retrieved.
 */
class MongoRegex
{
    /**
     * @var string
     */
    public $regex;

    /**
     * @var string
     */
    public $flags;

    /**
     * Creates a new regular expression
     *
     * @param string $regex - Regular expression string of the form
     *   /expr/flags.
     *
     * @return  - Returns a new regular expression.
     */
    public function __construct($regex)
    {
        $flagsStart = strrpos($regex, $regex[0]);  
        $this->regex = (string)substr($regex, 1, $flagsStart - 1);
        $this->flags = (string)substr($regex, $flagsStart + 1);

        if (!$this->regexIsValid($regex)) {
            throw new MongoException('invalid regex', MongoException::INVALID_REGEX);
        }
    }

    public function regexIsValid($regex)
    {
        return substr_count($regex, '/') >= 2 && 
          ((strlen($regex) && @preg_match($regex, null) !== false) || strlen($this->flags));
    }

    /**
     * A string representation of this regular expression
     *
     * @return string - This regular expression in the form "/expr/flags".
     */
    public function __toString()
    {
        return '/' . $this->regex . '/' . $this->flags;
    }
}



/**
 * The MongoResultException is thrown by several command helpers (such as
 * MongoCollection::findAndModify) in the event of failure. The original
 * result document is available through MongoResultException::getDocument.
 */
class MongoResultException extends Exception
{
    /**
     * Retrieve the full result document
     *
     * @return array - The full result document as an array, including
     *   partial data if available and additional keys.
     */
    public function getDocument()
    {
        throw new Exception('Not Implemented');
    }
}


/**
 * MongoTimestamp is used by sharding. If you're not looking to write sharding
 * tools, what you probably want is MongoDate.
 */
class MongoTimestamp
{
    /**
     * @var int
     */
    private static $globalInc = 0;

    /**
     * @var int
     */
    public $sec;

    /**
     * @var int
     */
    public $inc;

    /**
     * Creates a new timestamp.
     *
     * @param int $sec - Number of seconds since January 1st, 1970.
     * @param int $inc - Increment.
     *
     * @return  - Returns this new timestamp.
     */
    public function __construct($sec = -1, $inc = -1) {
        $this->sec = $sec < 0 ? time() : (int)$sec;
        if ($inc < 0) {
            $this->inc = self::$globalInc;
            self::$globalInc++;
        } else {
            $this->inc = (int) $inc;
        }
    }

    /**
     * Returns a string representation of this timestamp
     *
     * @return string - The seconds since epoch represented by this
     *   timestamp.
     */
    public function  __toString() {
        return (string)$this->sec;
    }
}



namespace Mongofill {

class Protocol
{
    const OP_REPLY = 1;
    const OP_MSG = 1000;
    const OP_UPDATE = 2001;
    const OP_INSERT = 2002;
    const OP_QUERY = 2004;
    const OP_GET_MORE = 2005;
    const OP_DELETE = 2006;
    const OP_KILL_CURSORS = 2007;

    const QF_TAILABLE_CURSOR = 2;
    const QF_SLAVE_OK = 4;
    const QF_OPLOG_REPLAY = 8;
    const QF_NO_CURSOR_TIMEOUT = 16;
    const QF_AWAIT_DATA = 32;
    const QF_PARTIAL = 64;

    const MSG_HEADER_SIZE = 16;

    private $socket;

    private static $lastRequestId = 3;

    public function __construct(Socket $socket)
    {
        $this->socket = $socket;
    }

    public function opInsert(
        $fullCollectionName,
        array $documents,
        array $options,
        $timeout
    )
    {
        $flags = 0;
        if (!empty($options['continueOnError'])) {
            $flags |= 1;
        }

        $data = pack(
            'Va*a*', 
            $flags, 
            "$fullCollectionName\0", 
            bson_encode_multiple($documents)
        );

        return $this->putWriteMessage(self::OP_INSERT, $data, $options, $timeout);
    }

    public function opUpdate(
        $fullCollectionName,
        array $query,
        array $update,
        array $options,
        $timeout
    )
    {
        $flags = 0;
        if (!empty($options['upsert'])) {
            $flags |= 1;
        } 
        
        if (!empty($options['multiple'])) {
            $flags |= 2;
        }

        $data = pack(
            'Va*Va*a*',0, 
            "$fullCollectionName\0", 
            $flags, 
            bson_encode($query), 
            bson_encode($update)
        );
        
        return $this->putWriteMessage(self::OP_UPDATE, $data, $options, $timeout);
    }

    public function opDelete(
        $fullCollectionName, 
        array $query,
        array $options,
        $timeout
    )
    {
        $flags = 0;
        if (!empty($options['justOne'])) {
            $flags |= 1;
        } 

        $data = pack(
            'Va*Va*',
            0, 
            "$fullCollectionName\0",
            $flags,
            bson_encode($query)
        );
        
        return $this->putWriteMessage(self::OP_DELETE, $data, $options, $timeout);
    }

    public function opQuery(
        $fullCollectionName, 
        array $query, 
        $numberToSkip, 
        $numberToReturn, 
        $flags,
        $timeout,
        array $returnFieldsSelector = null
    )
    {
        $data = pack(
            'Va*VVa*',
            $flags,
            "$fullCollectionName\0",
            $numberToSkip,
            $numberToReturn,
            bson_encode($query)
        );
        
        if ($returnFieldsSelector) {
            $data .= bson_encode($returnFieldsSelector);
        }
        
        return $this->putReadMessage(self::OP_QUERY, $data, $timeout);
    }

    public function opGetMore($fullCollectionName, $limit, $cursorId, $timeout)
    {
        $data = pack('Va*Va8', 0, "$fullCollectionName\0", $limit, Util::encodeInt64($cursorId));

        return $this->putReadMessage(self::OP_GET_MORE, $data, $timeout);
    }

    public function opKillCursors(
        array $cursors,
        array $options,
        $timeout
    )
    {
        $binCursors = array_reduce(
            $cursors,
            function($bin, $cursor) {
                return $bin .= Util::encodeInt64($cursor);
            },
            ''
        );

        $data = pack('VVa*', 0, count($cursors), $binCursors);

        return $this->putWriteMessage(self::OP_KILL_CURSORS, $data, $options, $timeout);
    }

    protected function putWriteMessage($opCode, $opData, array $options, $timeout)
    {
        return $this->socket->putWriteMessage($opCode, $opData, $options, $timeout);
    }

    protected function putReadMessage($opCode, $opData, $timeout)
    {
        return $this->socket->putReadMessage($opCode, $opData, $timeout);
    }
}


}


namespace Mongofill {

use MongoConnectionException;
use MongoCursorException;
use MongoCursorTimeoutException;

class Socket
{
    private $socket;
    private $host;
    private $port;

    private static $lastRequestId = 3;

    public function __construct($host, $port)
    {
        $this->host = $host;
        $this->port = $port;
    }

    public function connect()
    {
        if ($this->socket) {
            return true;
        }

        $this->createSocket();
        $this->connectSocket();
    }

    protected function createSocket()
    {
        $this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        if (!$this->socket) {
            throw new MongoConnectionException(sprintf(
                'error creating socket: %s',
                socket_strerror(socket_last_error())
            ));
        }
    }

    protected function connectSocket()
    {
        if (filter_var($this->host, FILTER_VALIDATE_IP)) {
            $ip = $this->host;
        } else {
            $ip = gethostbyname($this->host);
            if ($ip == $this->host) {
                throw new MongoConnectionException(sprintf(
                    'couldn\'t get host info for %s',
                    $this->host
                ));
            }
        }

        $connected = socket_connect($this->socket, $ip, $this->port);
        if (false === $connected) {
            throw new MongoConnectionException(sprintf(
                'unable to connect %s',
                socket_strerror(socket_last_error())
            ));
        }
    }

    public function disconnect()
    {
        if (null !== $this->socket) {
            socket_close($this->socket);
            $this->socket = null;
        }
    }

    public function putReadMessage($opCode, $opData, $timeout)
    {
        $requestId = $this->getNextLastRequestId();
        $payload = $this->packMessage($requestId, $opCode, $opData);

        $this->putMessage($payload);

        return $this->getMessage($requestId, $timeout);
    }

    public function putWriteMessage($opCode, $opData, array $options, $timeout)
    {
        $requestId = $this->getNextLastRequestId();
        $payload = $this->packMessage($requestId, $opCode, $opData);

        $lastError = $this->createLastErrorMessage($options);
        if ($lastError) {
            $requestId = $this->getNextLastRequestId();
            $payload .= $this->packMessage($requestId, Protocol::OP_QUERY, $lastError);
        }

        $this->putMessage($payload);

        if ($lastError) {
            $response = $this->getMessage($requestId, $timeout);
            return $response['result'][0];
        }

        return true;
    }

    protected function throwExceptionIfError(array $record)
    {
        if (!empty($record['err'])) {
            throw new MongoCursorException($record['err'], $record['code']);
        }
    }

    protected function getNextLastRequestId()
    {
        return self::$lastRequestId++;
    }

    protected function createLastErrorMessage(array $options)
    {
        $command = array_merge(['getLastError' => 1], $options);

        if (!isset($command['w'])) {
            $command['w'] = 1;
        }

        if (!isset($command['j'])) {
            $command['j'] = false;
        }

        if (!isset($command['wtimeout'])) {
            $command['wtimeout'] = 10000;
        }

        if ($command['w'] === 0 && $command['j'] === false) {
            return;
        }

        return pack('Va*VVa*', 0, "admin.\$cmd\0", 0, -1, bson_encode($command));
    }

    protected function packMessage($requestId, $opCode, $opData, $responseTo = 0xffffffff)
    {
        $bytes = strlen($opData) + Protocol::MSG_HEADER_SIZE;

        return pack('V4', $bytes, $requestId, $responseTo, $opCode) . $opData;
    }

    protected function putMessage($payload)
    {
        $bytesSent = 0;
        $bytes = strlen($payload);

        do {
            $result = socket_write($this->socket, $payload);
            if (false === $result) {
                throw new \RuntimeException('unhandled socket write error');
            }

            $bytesSent += $result;
            $payload = substr($payload, $bytesSent);
        } while ($bytesSent < $bytes);
    }

    protected function getMessage($requestId, $timeout)
    {
        $this->setTimeout($timeout);
        $header = $this->readHeaderFromSocket();
        if ($requestId != $header['responseTo']) {
            throw new \RuntimeException(sprintf(
                'request/cursor mismatch: %d vs %d',
                $requestId,
                $header['responseTo']
            ));
        }

        $data = $this->readFromSocket(
            $header['messageLength'] - Protocol::MSG_HEADER_SIZE
        );

        $header = substr($data, 0, 20);
        $vars = unpack('Vflags/V2cursorId/VstartingFrom/VnumberReturned', $header);

        $documents = bson_decode_multiple(substr($data, 20));
        if ($documents) {
            $this->throwExceptionIfError($documents[0]);
        } else {
            $documents = [];
        }

        return [
            'result'   => $documents,
            'cursorId' => Util::decodeInt64($vars['cursorId1'], $vars['cursorId2']) ?: null,
            'start'    => $vars['startingFrom'],
            'count'    => $vars['numberReturned'],
        ];
    }

    protected function readHeaderFromSocket()
    {
        $data = $this->readFromSocket(Protocol::MSG_HEADER_SIZE);
        $header = unpack('VmessageLength/VrequestId/VresponseTo/Vopcode', $data);

        return $header;
    }

    protected function readFromSocket($length)
    {
        $data = null;
        @socket_recv($this->socket, $data, $length, MSG_WAITALL);
        if (null === $data) {
            $this->handleSocketReadError();
            throw new \RuntimeException('unhandled socket read error');
        }

        return $data;
    }

    protected function handleSocketReadError()
    {
        $errno = socket_last_error($this->socket);
        if ($errno === 11 || $errno === 35) {
            throw new MongoCursorTimeoutException('Timed out waiting for data');
        }

        socket_clear_error($this->socket);
    }

    protected function setTimeout($timeoutInMs)
    {
        $secs = $timeoutInMs / 1000;
        $mili = $timeoutInMs % 1000;

        if (defined('HHVM_VERSION')) {
            socket_set_timeout($this->socket, (int) $secs, (int) $mili);
        } else {
            $timeout = ['sec' => (int) $secs, 'usec' => (int) $mili * 1000];
            socket_set_option($this->socket, SOL_SOCKET, SO_RCVTIMEO, $timeout);
        }
    }
}
}


namespace Mongofill {

class Util
{
    public static function encodeInt64($value)
    {
        $i1 = $value & 0xffffffff;
        $i2 = ($value >> 32) & 0xffffffff;

        return pack('V2', $i1, $i2);
    }

    public static function decodeInt64($i1, $i2 = null)
    {
        if (null !== $i2 && is_string($i1)) {
            $vars = Util::unpack('V2i', $i1, $offset, 8);
            extract($vars, EXTR_OVERWRITE);
        }

        return $i1 | ($i2 << 32);
    }
}

}