<?php
namespace eu\luige\plagiarism\entity;

class Preset extends Entity
{
    /** @Id @Column(type="integer") @GeneratedValue * */
    protected $id;
    /** @var  string[] @Column(type="json_array") */ 
    protected $serviceName;
    /** @var  string[] @Column(type="json_array") */ 
    protected $resourceProviderName;
    /** @var  string @Column(type="string") */
    protected $suiteName;

}