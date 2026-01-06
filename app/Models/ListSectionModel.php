<?php

namespace App\Models;

use CodeIgniter\Model;

class ListSectionModel extends Model
{
    protected $table = 'list_sections';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = ['list_id', 'title', 'position'];

    protected bool $allowEmptyInserts = false;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'list_id' => 'required|integer',
        'title' => 'required|max_length[255]',
    ];

    protected $validationMessages = [];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    /**
     * Get all sections for a specific list
     */
    public function getListSections($listId)
    {
        return $this->where('list_id', $listId)
            ->orderBy('position', 'ASC')
            ->findAll();
    }

    /**
     * Add a new section to a list
     */
    public function addSection($listId, $title, $position = null)
    {
        if ($position === null) {
            // Get the highest position and add 1
            $maxPosition = $this->where('list_id', $listId)
                ->selectMax('position')
                ->first();
            $position = ($maxPosition['position'] ?? -1) + 1;
        }

        return $this->insert([
            'list_id' => $listId,
            'title' => $title,
            'position' => $position,
        ]);
    }

    /**
     * Update section title
     */
    public function updateSectionTitle($sectionId, $title)
    {
        return $this->update($sectionId, ['title' => $title]);
    }

    /**
     * Delete a section (products will have section_id set to NULL)
     */
    public function deleteSection($sectionId)
    {
        return $this->delete($sectionId);
    }

    /**
     * Update positions for multiple sections
     */
    public function updatePositions($listId, $positions)
    {
        foreach ($positions as $sectionId => $position) {
            $this->where('id', $sectionId)
                ->where('list_id', $listId)
                ->set('position', $position)
                ->update();
        }
        return true;
    }

    /**
     * Get section with product count
     */
    public function getSectionWithProductCount($sectionId)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('list_sections ls');
        $builder->select('ls.*, COUNT(lp.id) as product_count');
        $builder->join('list_products lp', 'lp.section_id = ls.id', 'left');
        $builder->where('ls.id', $sectionId);
        $builder->groupBy('ls.id');
        
        return $builder->get()->getRowArray();
    }

    /**
     * Get all sections for a list with product counts
     */
    public function getListSectionsWithCounts($listId)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('list_sections ls');
        $builder->select('ls.*, COUNT(lp.id) as product_count');
        $builder->join('list_products lp', 'lp.section_id = ls.id', 'left');
        $builder->where('ls.list_id', $listId);
        $builder->groupBy('ls.id');
        $builder->orderBy('ls.position', 'ASC');
        
        return $builder->get()->getResultArray();
    }
}
