<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Complete</title>
</head>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Karla:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;0,800;1,200;1,300;1,400;1,500;1,600;1,700;1,800&display=swap');
    </style>
<body>
    <div style="margin: 20px auto;max-width: 750px;font-family: 'Karla', sans-serif;">
        <table style="table-layout: fixed;">
            <tr>
                <td style="text-align: center;">
                    @if(!empty($logo))
                        <img src="{{ $logo }}" alt="" style="width: 250px;">
                    @endif
                    <h1 style="margin-top: 30px; margin-bottom: 10px; font-size: 30px; text-transform: uppercase; font-weight: 500;">REGISTRATION COMPLETE</h1>
                </td>
            </tr>

            <tr>
                <td style="width: 100%;">
                    <p style="text-align: left; font-weight: 500; margin-top: 40px; margin-bottom: 10px;"> <span style="font-weight: 300;">Dear</span>   {{ $user->first_name }} {{ $user->last_name }},</p>
                    <p style="font-weight: 300;">Thank you for completing your registration with {{ $user->email }}</p>
                    <p style="font-weight: 300;">This email serves as a confirmation that your account is activated and that you are officially a part of the Hologram family.</p>
                    <p style="font-weight: 300;">Enjoy!</p>
                    <p style="font-weight: 500; margin-bottom: 30px;"><span style="font-weight: 300;">Best Regards, </span>  <br> Shop Hologram Inc. </p>
                </td>
            </tr>

            <?php
                $sql = "SELECT items.id, items.name, items.style_no, items.price, t.count
                FROM items
                LEFT JOIN (SELECT item_id, SUM(total_qty) count FROM order_items GROUP BY item_id) t ON items.id = t.item_id
                WHERE items.deleted_at IS NULL AND items.status = 1 AND items.activated_at >= (DATE(NOW()) - INTERVAL 30 DAY)
                ORDER BY count DESC
                LIMIT 4";

                $bestItems = \Illuminate\Support\Facades\DB::select($sql);

                $bestItemIds = [];

                foreach ($bestItems as $item)
                    $bestItemIds[] = $item->id;

                $bestItems = [];

                if (sizeof($bestItemIds) >= 4) {
                    $bestItems = App\Model\Item::select('id', 'style_no')
                        ->whereIn('id', $bestItemIds)
                        ->with('images')
                        ->orderByRaw(\Illuminate\Support\Facades\DB::raw("FIELD(id, " . implode(',', $bestItemIds) . " )"))
                        ->take(4)
                        ->get();
                } else {
                    $bestItems = App\Model\Item::select('id', 'style_no')
                        ->where('status', 1)
                        ->with('images')
                        ->orderBy('activated_at', 'desc')
                        ->limit(4)
                        ->get();
                }

                foreach ($bestItems as &$item) {
                    $image = App\Model\ItemImages::where('item_id', $item->id)
                        ->orderBy('sort', 'asc')
                        ->first();

                    $imagePath = asset('images/no-image.png');

                    if ($image)
                        $imagePath = Storage::url($image->compressed_image_path);

                    $item->image_path = $imagePath;
                }
            ?>
            <tr>
                <td style="width: 100%;">
                    <h2 style="text-align: center; width: 100%; padding-top: 20px; padding-bottom: 15px; border-top: 2px solid #000; margin-top: 10px; margin-bottom: 0; font-size: 20px; ">Best Sellers</h2>
                    <table style="width: 100%; margin-bottom: 0px;">
                        <tr>
                            @if(!empty($bestItems))
                                @foreach ($bestItems as $item)
                                    <td style="width: 25%; padding: 0px 5px; text-align: center;">
                                        <img src="{{ $item->image_path }}" alt="" style="width:100%">
                                        <p style="margin-top: 0; font-weight: 300;">{{ $item->style_no }}</p>
                                    </td>
                                @endforeach
                            @endif
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td style="text-align: center;">
                    <p style="font-weight: 300; margin-bottom: 20px; border-top: 2px solid #000; padding-top: 50px;">Contact Us</p>
                    <p style="font-weight: 300; margin-bottom: 10px;"> <span style="margin-right: 10px;">Shop Hologram</span> 3761 S Hill St #1, Los Angeles, CA 90007, USA</p>
                    <p style="font-weight: 300;"> COPYRIGHT © 2021 SHOPHOLOGRAM • SITE BY SHOPHOLOGRAM.COM</p>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
