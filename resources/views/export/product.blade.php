<table>
    <tr>
        <td style="width:10px;">#</td>
        <td style="width:30px;">Title</td>
        <td style="width:30px;">SubTitle</td>
        <td style="width:10px;">Brand</td>
        <td style="width:70px;">Category</td>
        <td style="width:70px;">Image Url</td>
        <td style="width:30px;">Seller Name</td>
        <td style="width:20px;">Price</td>
        <td style="width:20px;">Retail Price</td>
        <td style="width:20px;">Uploaded Quantity</td>
        <td style="width:20px;">Sold</td>
        <td style="width:20px;">Remains</td>
        <td style="width:30px;">Uploaded Date</td>
        <td style="width:30px;">Updated Date</td>
    </tr>
    @foreach ($products as $product)
    <tr>
        <td>
            {{$product['id']}}
        </td>
        <td>
            {{$product['title']}}
        </td>
        <td>
            {{$product['subtitle']}}
        </td>
        <td>
            {{$product['brand']}}
        </td>
        <td>
            {{$product['category']}}
        </td>
        <td>
            {{$product['img']}}
        </td>
        <td>
            {{$product['seller_name']}}
        </td>
        <td>
            {{$product['price']}}
        </td>
        <td>
            {{$product['retail_price']}}
        </td>
        <td>
            {{$product['quantity']}}
        </td>
        <td>
            {{$product['sell_count']}}
        </td>
        <td>
            {{$product['quantity'] - $product['sell_count']}}
        </td>
        <td>
            {{$product['created_at']}}
        </td>
        <td>
            {{$product['updated_at']}}
        </td>
    </tr>
    @endforeach
</table>