<?php


namespace eu\luige\plagiarism\entity;

/**
* @Entity
 * @Table(name="check")
 */
class Check extends Entity
{
    /** @Id @Column(type="integer") @GeneratedValue * */
    protected $id;
    
}