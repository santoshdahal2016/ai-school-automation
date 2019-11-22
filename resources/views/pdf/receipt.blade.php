<html>

<body>

<img width="100%" src="http://naamii.test/images/pdf/receipt_header.jpg">

<table style="width:100%;padding-left: 45px">
    <tr>
        <td>Participant’s Name: <b>{{$candidate->Name}}</b> <br>
            Category:<b> {{$candidate->category}}
                @if($candidate->Scholarship == 'Yes')
                    - Scholarship
                @endif
            </b>
        </td>
        <td>

        </td>
        <td>Date: <b>{{ date('d/m/Y') }}</b>
        </td>
    </tr>
</table>
<br>
<br>
<table style="width:90%;padding-left:45px; ">

    <tr style=" border: 1px solid black;">
        <td style=" border-bottom: 1px solid black;padding: 10px;"><b>Item</b>
        </td>
        <td style=" border-bottom: 1px solid black;">
            <b>Quantity</b>
        </td>
        <td style=" border-bottom: 1px solid black;"><b>Rate</b>
        </td>
    </tr>
    <tr>
        <td style=" border-bottom: 1px solid black;padding: 10px;">Registration Fee

        </td>
        <td style=" border-bottom: 1px solid black;">
        1
        </td>
        <td style=" border-bottom: 1px solid black;">{{$candidate->currency}}.{{$candidate->amount}}

        </td>
    </tr>

    <tr>
        <td>

        </td>
        <td>

        </td>
        <td style=" border: 1px solid black;padding: 10px;">Total: {{$candidate->currency}}.{{$candidate->amount}}


        </td>
    </tr>
</table>


<br>
<img src="http://naamii.test/images/pdf/receipt_footer.jpg">

</body>

</html>
