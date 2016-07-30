@extends('layout')

@section('content')
        <div class="content_field">
            <div class="container">
                <div class="col-md-12">
                    <div class="center_field col-md-offset-2 col-md-7 col-sm-12">
                        <div class="new_tag">
                            <div class="tags">
                                <h1>Рейтинги</h1>
                            </div>

                            <ul class="ratings_tags_list list-inline">
                                @foreach($ranks as $rank_name => $rank_list)
                                    <?php $function =  $rank_name."Songs" ?>
                                    <li class="ratings_list well" >
                                    <p>
                                        {{ $rank_list['tittle'] }}
                                    </p>
                                    <ul class="max-list-width list-unstyled rank_list_min">
                                        @foreach($rank_list['data'] as $data)
                                            <li>
                                                    <div class="col-md-9 col-sm-9 col-xs-9 name">
                                                        <a href="{{ url("songs?$rank_name=$data->id") }}">{{ $data->name }} </a>
                                                    </div>
                                                    <div class="col-md-2 col-sm-2 col-xs-2">
                                                        {{ $data->$function()->count() }}

                                                    </div>
                                            </li>
                                        @endforeach
                                    </ul>

                                    <div class="show_all col-md-12 col-sm-12 col-xs-12">
                                        <a href="#"  data-toggle="modal" data-target="#{{ $rank_name }}"> Показать все </a>
                                    </div>

                                    <div class="modal fade" id="{{ $rank_name }}" tabindex="-1" role="dialog" aria-labelledby="myModal{{ $rank_name }}Label">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">
                                                            &times;
                                                        </span>
                                                    </button>
                                                    <h4 class="modal-title" id="myModal{{ $rank_name }}Label">
                                                        {{ $rank_list['tittle'] }}
                                                    </h4>
                                                </div>
                                                <div class="modal-body">
                                                    @if($rank_name=="performer" || $rank_name=="composer")
                                                        <div class="list_character">
                                                            <ul class="pagination ratings-alphabet">
                                                                <li class="character active" data-symbol="0">0-9</li>
                                                                @for($i=65;$i<90;$i++)
                                                                    <li class="character" data-symbol="{{ chr($i) }}">
                                                                        {{ chr($i) }}
                                                                    </li>
                                                                @endfor
                                                            </ul>
                                                            <ul class="pagination ratings-alphabet">
                                                            @foreach(range(chr(0xC0), chr(0xDF)) as $cyrylic_symbol)
                                                                    <li class="character" data-symbol="{{ iconv('CP1251', 'UTF-8', $cyrylic_symbol) }}">
                                                                        {{ iconv('CP1251', 'UTF-8', $cyrylic_symbol) }}
                                                                    </li>
                                                            @endforeach
                                                            </ul>
                                                        </div>
                                                    @endif
                                                    <ul class="list-unstyled all_ranks">
                                                        @foreach($rank_list['data'] as $data)
                                                            <li>
                                                                    <div class="col-md-9 col-sm-9 col-xs-9">
                                                                        <a href="{{ url("songs?$rank_name=$data->id") }}">{{ $data->name }} </a>
                                                                    </div>
                                                                    <div class="col-md-2 col-sm-2 col-xs-2">
                                                                        {{ $data->$function()->count() }}
                                                                    </div>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                    <ul class="pagination number_pagination">
                                                        @if($rank_name=="country")
                                                            @for($i=1;$i<$pagination_list+1;$i++)
                                                                @if($i==1)
                                                                <li class="paginate_number active" data-paginate="{{ $i }}">
                                                                    <span>{{ $i }}</span>
                                                                </li>
                                                                @else
                                                                <li class="paginate_number" data-paginate="{{ $i }}">
                                                                    <span>{{ $i }}</span>
                                                                </li>
                                                                @endif
                                                            @endfor
                                                        @endif
                                                    </ul>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-default" data-dismiss="modal">
                                                        Закрыть
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>

                </div>
            </div>
        </div>
@stop

@section('js')
    <script type="text/javascript">
        $('body').on('click','.paginate_number',function(){
            var modal_window = $(this).closest('.modal');
            var symbol = modal_window.first().find('.ratings-alphabet').find('.active').data('symbol');
            var modal_window = $(this).closest('.modal');
            var list_type = modal_window[0].id;
            var paginate_number = $(this).data('paginate');

            if($(this).hasClass('active'))
                return;

            $('.paginate_number').removeClass('active');
            $(this).addClass("active");

            controllRankLists(symbol, paginate_number, list_type, {{ $limit }} );
        });

        $('.character').on("click", function(){
            var modal_window = $(this).closest('.modal');
            var symbol = $(this).data('symbol');
            var list_type = modal_window[0].id;
            var paginate_number = 1;

            if($(this).hasClass('active'))
                return;

            $('.character').removeClass('active');
            $(this).addClass("active");

            controllRankLists(symbol, paginate_number, list_type, {{ $limit }});

        });

        function controllRankLists(symbol,paginate_number,list_type,limit){
            $.ajax
            ({
                url: "{{ action('RanksListController@getRankList') }}",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                },
                method: 'POST',
                data: {'symbol':symbol,'number':paginate_number,'list_type':list_type,'limit':{{ $limit }} },
                success: function(result){
                    $('#'+list_type).find('.all_ranks').remove();
                    $('#'+list_type).find('.number_pagination').remove();
                    var list = $("<ul class='list-unstyled all_ranks'></ul>");
                    var pagination = $("<ul class='pagination number_pagination'></ul>");

                    if(result.paginate_count)
                    {
                        for(var i=0;i<result.paginate_count;i++)
                        {
                            if(i==paginate_number-1)
                                pagination.append(`<li class="paginate_number active" data-paginate="`+(i+1)+`">
                                    <a href="#">`+(i+1)+`</a>
                                </li>`);
                            else
                                pagination.append(`<li class="paginate_number" data-paginate="`+(i+1)+`">
                                    <a href="#">`+(i+1)+`</a>
                                </li>`);
                        }

                        $('#'+list_type).find('.modal-body').append(pagination);

                        result.ranks.forEach(function(item, i, result){
                                var url=`http://`+window.location.host+`\\`+"songs?"+list_type+"="+item.id;
                                list.append(
                                    `<div class="col-md-9 col-sm-9 col-xs-9">
                                     <a href="`+url+`">`+item.name+`</a>
                                     </div>
                                     <div class="col-md-2 col-sm-2 col-xs-2">`
                                     +item.count+
                                     `</div>`
                                );
                                });
                    }
                    else
                        list.append(
                            `<li>Ничего не найденно</li>`
                            );
                    list.insertBefore($('#'+list_type).find(".number_pagination"));
                }
            })
        }
    </script>
@stop