<?php

namespace App\Models\Agency;

use App\Models\Account\AccountManager;
use App\Core\Image\SaveImage;
use App\Models\Notification\Notification;
use Auth;
use DB;

class AgencyManager
{
    public function store($data)
    {
        $data = $this->processSave($data);
        $agency = new Agency($data);
        if (Auth::guard('admin')->guest()) {
            $agency->top = Agency::NOT_TOP;
            $agency->blocked = Agency::NOT_BLOCKED;
        }
        $accountManager = new AccountManager();
        $agency->hash = $accountManager->generateRandomUniqueHash();
        $agency->status = '';
        SaveImage::save($data['image'], $agency);
        SaveImage::save($data['cover'], $agency, 'cover');

        DB::transaction(function() use($data, $agency) {
            $agency->save();
            $this->storeMl($data['ml'], $agency);
        });

        $notification = new Notification();
        $notification->send(route('admin_agency_edit', $agency->id), 'agency');
    }

    public function update($id, $data)
    {
        $agency = Agency::where('id', $id)->firstOrFail();
        $data = $this->processSave($data);
        if (!empty($data['password'])) {
            $agency->password = bcrypt($data['password']);
        }
        if (Auth::guard('admin')->guest()) {
            $data['top'] = $agency->top;
            $data['blocked'] = $agency->blocked;
            $data['show_status'] = $agency->show_status;
        }
        SaveImage::save($data['image'], $agency);
        SaveImage::save($data['cover'], $agency, 'cover');

        DB::transaction(function() use($data, $agency) {
            $agency->update($data);
            $this->updateMl($data['ml'], $agency);
        });
    }

    protected function processSave($data)
    {
        if (!isset($data['top'])) {
            $data['top'] = Agency::NOT_TOP;
        }
        if (!isset($data['blocked'])) {
            $data['blocked'] = Agency::NOT_BLOCKED;
        }
        $data['active'] = Agency::ACTIVE;
        return $data;
    }

    protected function storeMl($data, Agency $agency)
    {
        $ml = [];
        foreach ($data as $lngId => $mlData) {
            $mlData['lng_id'] = $lngId;
            $ml[] = new AgencyMl($mlData);
        }
        $agency->ml()->saveMany($ml);
    }

    protected function updateMl($data, Agency $agency)
    {
        AgencyMl::where('id', $agency->id)->delete();
        $this->storeMl($data, $agency);
    }

    public function delete($id)
    {
        DB::transaction(function() use($id) {
            Agency::where('id', $id)->delete();
            AgencyMl::where('id', $id)->delete();
        });
    }
}