0.4.0 Update Notes
==================

1. Broken backward compatibility
--------------------------------

Version 0.4.0 breaks backward compatibility at load sub-views into the main view file. The change is minor, and should be done easily. Versions before 0.4.0 accepted a simple array, with the sub-view name as key and the view as the value, or a simple nested array, where the key was still the sub-view name and the value was an array with the view file name and a **mandatory** data array:
```PHP
// as it was before
$this->subViews = array(
    "name"  =>  "path/to/file"
);
$this->subViews = array(
    "name"  =>  array(
        "view"  =>  "path/to/file",
        "data"  =>  array(
            "param" =>  "value"
        )
    )
);
```

The first way is now not available anymore, and the second way must be updated. The first part is still the same, where the first key is the name of the sub-view, but its value is a nested array. The bottom most array now holds the view file name, and **optional** data array.
```PHP
// as it is now
$this->subViews = array(
    "name"  =>  array(
        array(
            "view"  =>  "path/to/file",
            "data"  =>  array(
                "param" =>  "value"
            )
        )
    )
);
```
