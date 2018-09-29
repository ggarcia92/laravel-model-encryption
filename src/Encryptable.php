<?php namespace Magros\Encryptable;

trait Encryptable
{

    public $aesKey = null;
    public static $enableEncryption = true;
    private $encrypter;

    /**
     * @return Encrypter
     */
    public function encrypter()
    {
        if(! $this->encrypter){
            $this->encrypter = new Encrypter();
        }
        return $this->encrypter;
    }

    /**
     * @return mixed
     */
    public function getEncryptableAttributes()
    {
        return $this->encryptable;
    }

    /**
     * @param $key
     * @return bool
     */
    public function encryptable($key)
    {
        if(self::$enableEncryption){
            return in_array($key, $this->encryptable);
        }
        return false;
    }

    /**
     * Decrypt a value.
     *
     * @param $value
     *
     * @return string
     * @throws \Exception
     */
    public function decryptAttribute($value)
    {
       return $this->encrypter()->decrypt($value);
    }


    /**
     * @param $value
     * @return string
     * @throws \Exception
     */
    public function encryptAttribute($value)
    {
        return $this->encrypter()->encrypt($value);
    }

    /**
     * @param $key
     * @return string
     * @throws \Exception
     */
    public function getAttribute($key)
    {
        $value = parent::getAttribute($key);

        if ($this->encryptable($key)) {

            $value = $this->decryptAttribute($value);
        }

        return $value;
    }


    /**
     * @param $key
     * @param $value
     * @return mixed
     * @throws \Exception
     */
    public function setAttribute($key, $value)
    {
        if ($this->encryptable($key)) {
            $value = $this->encryptAttribute($value);
        }

        return parent::setAttribute($key, $value);
    }


    /**
     * @return mixed
     * @throws \Exception
     */
    public function getArrayableAttributes()
    {
        $attributes = parent::getArrayableAttributes();

        foreach ($attributes as $key => $attribute) {
            if ($this->encryptable($key)) {
                $attributes[$key] = $this->decryptAttribute($attribute);
            }
        }

        return $attributes;
    }

    /**
     * @return EncryptableQueryBuilder
     */
    public function newBaseQueryBuilder()
    {
        $connection = $this->getConnection();

        return new EncryptableQueryBuilder($connection, $this);
    }
}