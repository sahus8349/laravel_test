<?php


namespace App\Http\Controllers;

use App\Models\MenuItem;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;

class MenuController extends BaseController
{
    /*
    Requirements:
    - the eloquent expressions should result in EXACTLY one SQL query no matter the nesting level or the amount of menu items.
    - it should work for infinite level of depth (children of childrens children of childrens children, ...)
    - verify your solution with `php artisan test`
    - do a `git commit && git push` after you are done or when the time limit is over

    Hints:
    - open the `app/Http/Controllers/MenuController` file
    - eager loading cannot load deeply nested relationships
    - a recursive function in php is needed to structure the query results
    - partial or not working answers also get graded so make sure you commit what you have


    Sample response on GET /menu:
    ```json
    [
        {
            "id": 1,
            "name": "All events",
            "url": "/events",
            "parent_id": null,
            "created_at": "2021-04-27T15:35:15.000000Z",
            "updated_at": "2021-04-27T15:35:15.000000Z",
            "children": [
                {
                    "id": 2,
                    "name": "Laracon",
                    "url": "/events/laracon",
                    "parent_id": 1,
                    "created_at": "2021-04-27T15:35:15.000000Z",
                    "updated_at": "2021-04-27T15:35:15.000000Z",
                    "children": [
                        {
                            "id": 3,
                            "name": "Illuminate your knowledge of the laravel code base",
                            "url": "/events/laracon/workshops/illuminate",
                            "parent_id": 2,
                            "created_at": "2021-04-27T15:35:15.000000Z",
                            "updated_at": "2021-04-27T15:35:15.000000Z",
                            "children": []
                        },
                        {
                            "id": 4,
                            "name": "The new Eloquent - load more with less",
                            "url": "/events/laracon/workshops/eloquent",
                            "parent_id": 2,
                            "created_at": "2021-04-27T15:35:15.000000Z",
                            "updated_at": "2021-04-27T15:35:15.000000Z",
                            "children": []
                        }
                    ]
                },
                {
                    "id": 5,
                    "name": "Reactcon",
                    "url": "/events/reactcon",
                    "parent_id": 1,
                    "created_at": "2021-04-27T15:35:15.000000Z",
                    "updated_at": "2021-04-27T15:35:15.000000Z",
                    "children": [
                        {
                            "id": 6,
                            "name": "#NoClass pure functional programming",
                            "url": "/events/reactcon/workshops/noclass",
                            "parent_id": 5,
                            "created_at": "2021-04-27T15:35:15.000000Z",
                            "updated_at": "2021-04-27T15:35:15.000000Z",
                            "children": []
                        },
                        {
                            "id": 7,
                            "name": "Navigating the function jungle",
                            "url": "/events/reactcon/workshops/jungle",
                            "parent_id": 5,
                            "created_at": "2021-04-27T15:35:15.000000Z",
                            "updated_at": "2021-04-27T15:35:15.000000Z",
                            "children": []
                        }
                    ]
                }
            ]
        }
    ]
     */

    public function getMenuItems() {
        $menuitem = MenuItem::all();

        $menu_arr = array();
        $children = array();
        $children1 = array();
        foreach($menuitem AS $value){
            $children[$value["id"]] = $value;
            $children[$value["id"]]['children'] = [];

            if(!empty($value["parent_id"])){
                $children1[$value["parent_id"]][] = $value;
            }
        }

        foreach($children AS $value){
            $parent_id = $value["parent_id"];
            if(!empty($parent_id)){
                $parent = $children[$parent_id];
                if(!empty($parent["parent_id"])){
                    $master_parent = $children[$parent["parent_id"]];
                }
            }

            if(!empty($master_parent)){
                // $menu_arr[$master_parent["id"]]["children"][$parent["id"]]["children"] = $children1[$value["parent_id"]];
            }elseif(!empty($parent)){
                $children2 = $children1[$parent["id"]];
                foreach($children2 AS $k=>$v){
                    if(!empty($children1[$v["id"]])){
                        $children2[$k]["children"] = array_values($children1[$v["id"]]);
                    }
                }
                $menu_arr[$parent["id"]]["children"] = array_values($children2);
            }else{
                $menu_arr[$value["id"]] = $value;
            }
        }

        return response()->json($menu_arr);
    }
}
