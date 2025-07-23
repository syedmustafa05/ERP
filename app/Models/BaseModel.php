<?php

namespace App\Models;

/**
 * Base Model Class
 * Provides basic CRUD operations and database interaction
 */
abstract class BaseModel
{
    protected $table;
    protected $primaryKey = 'id';
    protected $fillable = [];
    protected $guarded = [];
    protected $casts = [];
    protected $dates = ['created_at', 'updated_at'];
    protected $timestamps = true;
    
    protected static $pdo;
    
    public function __construct(array $attributes = [])
    {
        $this->fill($attributes);
        
        if (!static::$pdo) {
            static::$pdo = $GLOBALS['pdo'];
        }
    }
    
    /**
     * Fill the model with an array of attributes
     */
    public function fill(array $attributes)
    {
        foreach ($attributes as $key => $value) {
            if ($this->isFillable($key)) {
                $this->$key = $value;
            }
        }
        return $this;
    }
    
    /**
     * Check if attribute is fillable
     */
    protected function isFillable($key)
    {
        if (in_array($key, $this->guarded)) {
            return false;
        }
        
        if (empty($this->fillable)) {
            return true;
        }
        
        return in_array($key, $this->fillable);
    }
    
    /**
     * Find all records
     */
    public static function all()
    {
        $instance = new static();
        $stmt = static::$pdo->query("SELECT * FROM {$instance->table} ORDER BY created_at DESC");
        return $stmt->fetchAll();
    }
    
    /**
     * Find record by ID
     */
    public static function find($id)
    {
        $instance = new static();
        $stmt = static::$pdo->prepare("SELECT * FROM {$instance->table} WHERE {$instance->primaryKey} = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /**
     * Create new record
     */
    public static function create(array $attributes)
    {
        $instance = new static($attributes);
        return $instance->save();
    }
    
    /**
     * Save the model
     */
    public function save()
    {
        if (isset($this->{$this->primaryKey})) {
            return $this->update();
        } else {
            return $this->insert();
        }
    }
    
    /**
     * Insert new record
     */
    protected function insert()
    {
        $attributes = $this->getAttributes();
        
        if ($this->timestamps) {
            $attributes['created_at'] = date('Y-m-d H:i:s');
        }
        
        $fields = array_keys($attributes);
        $values = array_values($attributes);
        $placeholders = str_repeat('?,', count($fields) - 1) . '?';
        
        $sql = "INSERT INTO {$this->table} (" . implode(',', $fields) . ") VALUES ({$placeholders})";
        $stmt = static::$pdo->prepare($sql);
        $stmt->execute($values);
        
        $this->{$this->primaryKey} = static::$pdo->lastInsertId();
        
        return $this->fresh();
    }
    
    /**
     * Update existing record
     */
    protected function update()
    {
        $attributes = $this->getAttributes();
        
        if ($this->timestamps) {
            $attributes['updated_at'] = date('Y-m-d H:i:s');
        }
        
        unset($attributes[$this->primaryKey]);
        
        $fields = array_keys($attributes);
        $values = array_values($attributes);
        $values[] = $this->{$this->primaryKey};
        
        $setClause = implode(' = ?, ', $fields) . ' = ?';
        $sql = "UPDATE {$this->table} SET {$setClause} WHERE {$this->primaryKey} = ?";
        $stmt = static::$pdo->prepare($sql);
        $stmt->execute($values);
        
        return $this->fresh();
    }
    
    /**
     * Delete the model
     */
    public function delete()
    {
        $stmt = static::$pdo->prepare("DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?");
        return $stmt->execute([$this->{$this->primaryKey}]);
    }
    
    /**
     * Delete by ID
     */
    public static function destroy($id)
    {
        $instance = new static();
        $stmt = static::$pdo->prepare("DELETE FROM {$instance->table} WHERE {$instance->primaryKey} = ?");
        return $stmt->execute([$id]);
    }
    
    /**
     * Get fresh instance from database
     */
    public function fresh()
    {
        return static::find($this->{$this->primaryKey});
    }
    
    /**
     * Get model attributes
     */
    protected function getAttributes()
    {
        $attributes = [];
        foreach ($this->fillable as $field) {
            if (isset($this->$field)) {
                $attributes[$field] = $this->$field;
            }
        }
        return $attributes;
    }
    
    /**
     * Where clause
     */
    public static function where($column, $operator = '=', $value = null)
    {
        if ($value === null) {
            $value = $operator;
            $operator = '=';
        }
        
        $instance = new static();
        $stmt = static::$pdo->prepare("SELECT * FROM {$instance->table} WHERE {$column} {$operator} ?");
        $stmt->execute([$value]);
        return $stmt->fetchAll();
    }
    
    /**
     * Count records
     */
    public static function count()
    {
        $instance = new static();
        $stmt = static::$pdo->query("SELECT COUNT(*) FROM {$instance->table}");
        return $stmt->fetchColumn();
    }
    
    /**
     * Magic getter
     */
    public function __get($key)
    {
        return $this->$key ?? null;
    }
    
    /**
     * Magic setter
     */
    public function __set($key, $value)
    {
        $this->$key = $value;
    }
}