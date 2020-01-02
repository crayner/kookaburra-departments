<?php
/**
 * Created by PhpStorm.
 *
 * Kookaburra
 *
 * (c) 2018 Craig Rayner <craig@craigrayner.com>
 *
 * User: craig
 * Date: 23/11/2018
 * Time: 15:27
 */
namespace Kookaburra\Departments\Entity;

use App\Manager\EntityInterface;
use App\Util\TranslationsHelper;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Department
 * @package Kookaburra\Departments\Entity
 * @ORM\Entity(repositoryClass="Kookaburra\Departments\Repository\DepartmentRepository")
 * @ORM\Table(options={"auto_increment": 1}, name="Department",
 *     uniqueConstraints={@ORM\UniqueConstraint(name="name",columns={ "name"}), @ORM\UniqueConstraint(name="nameShort",columns={ "nameShort"})}
 * )
 */
class Department implements EntityInterface
{
    /**
     * @var integer|null
     * @ORM\Id()
     * @ORM\Column(type="smallint", columnDefinition="INT(4) UNSIGNED ZEROFILL")
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(length=16, options={"default": "Learning Area"})
     * @Assert\Choice({"Learning Area","Administration"})
     */
    private $type = "Learning Area";

    /**
     * @var array
     */
    private static $typeList = ['Learning Area', 'Administration'];

    /**
     * @var string
     * @ORM\Column(length=40,unique=true)
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(length=4,name="nameShort",unique=true)
     * @Assert\NotBlank()
     */
    private $nameShort;

    /**
     * @var array
     * @ORM\Column(type="simple_array",name="subjectListing")
     */
    private $subjectListing = [];

    /**
     * @var string|null
     * @ORM\Column(type="text", nullable=true)
     */
    private $blurb;

    /**
     * @var string|null
     * @ORM\Column()
     */
    private $logo;

    /**
     * @var DepartmentStaff|null
     * @ORM\OneToMany(targetEntity="Kookaburra\Departments\Entity\DepartmentStaff", mappedBy="department", orphanRemoval=true)
     */
    private $staff;

    /**
     * @var Collection|DepartmentResource[]|null
     * @ORM\OneToMany(targetEntity="Kookaburra\Departments\Entity\DepartmentResource", mappedBy="department", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $resources;

    /**
     * Department constructor.
     */
    public function __construct()
    {
        $this->resources = new ArrayCollection();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     * @return Department
     */
    public function setId(?int $id): Department
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return Department
     */
    public function setType(string $type): Department
    {
        $this->type = in_array($type, self::getTypeList()) ? $type : 'Learning Area';
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Department
     */
    public function setName(string $name): Department
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getNameShort(): string
    {
        return $this->nameShort;
    }

    /**
     * @param string $nameShort
     * @return Department
     */
    public function setNameShort(string $nameShort): Department
    {
        $this->nameShort = $nameShort;
        return $this;
    }

    /**
     * @return array
     */
    public function getSubjectListing(): array
    {
        $this->subjectListing =  $this->subjectListing ?: [];
        foreach($this->subjectListing as $q=>$w)
            $this->subjectListing[$q] = trim($w);
        return $this->subjectListing;
    }

    /**
     * SubjectListing.
     *
     * @param array $subjectListing
     * @return Department
     */
    public function setSubjectListing(array $subjectListing): Department
    {
        dump($subjectListing);
        $this->subjectListing = $subjectListing;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getBlurb(): ?string
    {
        return $this->blurb;
    }

    /**
     * @param string|null $blurb
     * @return Department
     */
    public function setBlurb(?string $blurb): Department
    {
        $this->blurb = $blurb;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getLogo(): ?string
    {
        return $this->logo;
    }

    /**
     * @param string|null $logo
     * @return Department
     */
    public function setLogo(?string $logo): Department
    {
        $this->logo = $logo;
        return $this;
    }

    /**
     * @return array
     */
    public static function getTypeList(): array
    {
        return self::$typeList;
    }

    /**
     * getStaff
     * @return Collection|null
     */
    public function getStaff(): ?Collection
    {
        if (null === $this->staff)
            $this->staff = new ArrayCollection();

        if ($this->staff instanceof PersistentCollection)
            $this->staff->initialize();

        $iterator = $this->staff->getIterator();

        $iterator->uasort(
            function ($a, $b) {
                return $a->getPerson()->formatName(['style' => 'long', 'reverse' => true]) < $b->getPerson()->formatName(['style' => 'long', 'reverse' => true]) ? -1 : 1 ;
            }
        );

        $this->staff  = new ArrayCollection(iterator_to_array($iterator, false));

        return $this->staff;
    }

    /**
     * Staff.
     *
     * @param DepartmentStaff|null $staff
     * @return Department
     */
    public function setStaff(?Collection $staff): Department
    {
        $this->staff = $staff;
        return $this;
    }

    /**
     * getResources
     * @return Collection
     */
    public function getResources(): Collection
    {
        if (null === $this->resources)
            $this->resources = new ArrayCollection();
        if ($this->resources instanceof PersistentCollection)
            $this->resources->initialize();

        return $this->resources;
    }

    /**
     * Resources.
     *
     * @param Collection|null $resources
     * @return Department
     */
    public function setResources(?Collection $resources): Department
    {
        $this->resources = $resources;
        return $this;
    }

    /**
     * __toString
     * @return string
     */
    public function __toString(): string
    {
        return $this->getName() . ' (' . $this->getNameShort() . ')';
    }

    public function toArray(?string $name = null): array
    {
        return [
            'name' => $this->getName(),
            'abbr' => $this->getNameShort(),
            'type' => TranslationsHelper::translate($this->getType()),
            'canDelete' => true,
            'staff' => $this->getStaffNames(),
        ];
    }

    /**
     * getStaffNames
     * @return string
     */
    public function getStaffNames(): string
    {
        $result = [];
        foreach($this->getStaff() as $staff)
            $result[] = $staff->getPerson()->formatName(['style' => 'long', 'reverse' => true]);
        if (empty($result))
            $result[] = TranslationsHelper::translate('None', [], 'Departments');
        return implode("\n<br/>", $result);
    }
}