@extends('layouts.app')

@section('title', 'Kezdőlap')

@section('content')
    <div class="container">
        <div class="jumbotron">
            <div class="row">
                <p class="col-md-3" style="font-weight:bold">Jelenlegi életerő: <span id="health"  style="font-size: 1.3rem;">{{$user->health}}<span></p>
                <p class="col-md-3" style="font-weight:bold">Elért pontszám: <span id="points" style="font-size: 1.3rem;">{{$user->points}}<span></p>
                <p class="col-md-3" style="font-weight:bold">Megszerzett érmék: <span id="gold" style="font-size: 1.3rem;">{{$user->gold}}<span></p>
                <p class="col-md-3" style="font-weight:bold">Szabad lépések: <span id="steps" style="font-size: 1.3rem;">{{$user->remaining_steps}}<span></p>
            </div>
            <div class="row">
                <table class="gameTable col-md-6" data-id="{{$table_id}}">
                    <tbody>
                        <!-- //0 - not visited
                        //1 - start
                        //2 - your place
                        //3 - already visited
                        //4 - visitable (not visited yet)
                        //5 - end -->
                        @php 
                            $col_count = 0;
                            $row_count = 0;
                        @endphp
                        @foreach ($table as $row)
                            <tr>
                                @foreach ($row as $item)
                                    <td data-id="{{$row_count.'-'.$col_count}}" class="{{$item===4 ? 'clickable':''}}" 
                                    style="width:70px;height:70px;border:1px solid black;background-color:
                                        {{ $item===5 ? 'yellow' : ''}}
                                        {{ $item===4 ? 'green' : ''}}
                                        {{ $item===3 ? 'blue' : ''}}
                                        {{ $item===2 ? 'red' : ''}}
                                        {{ $item===1 ? 'white' : ''}}
                                        {{ $item===0 ? 'gray' : ''}}
                                    "></td>
                                    @php $col_count++; @endphp
                                @endforeach
                            </tr>
                            @php $row_count++; @endphp
                            @php $col_count=0; @endphp
                        @endforeach
                    </tbody>
                </table>
                <div class="col-md-6" style="border:1px solid black;" id="roomInfo">
                </div>
            </div>
        </div>
    </div>

    <script>
         $(document).ready(function() {
            $('body').on('click' , '.clickable', function(e) {
               e.preventDefault();
               $.ajaxSetup({
                    beforeSend: function(xhr, type) {
                        if (!type.crossDomain) {
                            xhr.setRequestHeader('X-CSRF-Token', $('meta[name="csrf-token"]').attr('content'));
                        }
                    },
               });
               $.ajax({
                  url: "{{ url('/step') }}",
                  type: 'POST',
                  data: {
                     clickedId: $(this).data("id"),
                     clickedTableId: $('.gameTable').data("id")
                  },
                  success: function(result){
                        if(!result.win){
                            new_table = result.table;
                            resultHTML = "";
                            col_count = 0;
                            row_count = 0;
                            resultHTML += "<tbody>";
                            new_table.forEach(row => {
                                resultHTML += "<tr>";
                                row.forEach(element => {
                                    resultHTML += `<td data-id="` + row_count + `-` + col_count + `"`;
                                    
                                    if(element === 4)
                                        resultHTML += ` class="clickable"`;    
                                    
                                    resultHTML += ` style="width:70px;height:70px;border:1px solid black;background-color:`;
                                    
                                    if(element === 5)
                                        resultHTML += 'yellow';
                                    else if(element === 4)
                                        resultHTML += 'green';
                                    else if(element === 3)
                                        resultHTML += 'blue';
                                    else if(element === 2)
                                        resultHTML += 'red';
                                    else if(element === 1)
                                        resultHTML += 'white';
                                    else if(element === 0)
                                        resultHTML += 'gray';

                                    resultHTML += `"></td>`;
                                    col_count++;
                                });
                                resultHTML += "</tr>";
                                row_count++;
                                col_count = 0;
                            });
                            resultHTML += "</tbody>";
                            $('.gameTable').html(resultHTML);
                            result_type = result.room.type;
                            if(result_type == "FREE"){
                                $('#roomInfo').html(`<h2 style="font-weight:bold">Szerencsédre nem botlottál akadályba!</h2>`);
                            }
                            else if(result_type == "ENEMY"){
                                enemy_type = result.enemy.type;
                                enemy_name = result.enemy.name;
                                $('#roomInfo').html(`<h2 style="font-weight:bold">Utad során ` + enemy_name +`, a(z) `+ enemy_type +` állta utad!</h2>`);
                            }
                            else if(result_type == "BARRIER"){
                                $('#roomInfo').html(`<h2 style="font-weight:bold">Szerencsédre nem botlottál akadályba!</h2>`);
                            }
                            user = result.user;
                            $('#steps').text(user.remaining_steps);
                            $('#gold').text(user.gold);
                            $('#health').text(user.health);
                            $('#points').text(user.points);
                        }
                        else{
                            window.location.href = '/';
                        }
                  },
                  error: function (data, textStatus, errorThrown) {
                    console.log(data);
                  }
                });
            });
        })
    </script>
@endsection
