@php use App\Models\Submit; @endphp
<div class="cr-nav">
    <nav>
        <ul>
            <li>
                <a href="" class="active" id="submit-instant">
                    <span><i class='bx bxs-circle' style='color:{{Submit::mapSettings('instant')['navColor']}}'></i> فوری ({{$instantCount}})</span>
                </a>
            </li>
            <li>
                <a href="" class="active" id="submit-9">
                    <span><i class='bx bxs-circle' style='color:{{Submit::mapSettings('9')['navColor']}}'></i> ۹ تا ۱۲ ({{$submit9Count}})</span>
                </a>
            </li>
            <li>
                <a href="" class="active" id="submit-11">
                    <span><i class='bx bxs-circle' style='color:{{Submit::mapSettings('11')['navColor']}}'></i> ۱۱ تا ۱۴ ({{$submit11Count}})</span>
                </a>
            </li>
            <li>
                <a href="" class="active" id="submit-13">
                    <span><i class='bx bxs-circle' style='color:{{Submit::mapSettings('13')['navColor']}}'></i> ۱۳ تا ۱۶ ({{$submit13Count}})</span>
                </a>
            </li>
            <li>
                <a href="" class="active" id="submit-15">
                    <span><i class='bx bxs-circle' style='color:{{Submit::mapSettings('15')['navColor']}}'></i> ۱۵ تا ۱۸ ({{$submit15Count}})</span>
                </a>
            </li>
            <li>
                <a href="" class="active" id="submit-17">
                    <span><i class='bx bxs-circle' style='color:{{Submit::mapSettings('17')['navColor']}}'></i> ۱۷ تا ۲۰ ({{$submit17Count}})</span>
                </a>
            </li>
            <li>
                <a href="" class="@if(isset($driver)) active @endif" id="submit-active">
                    <span><i class='bx bxs-circle' style='color:{{Submit::mapSettings('active')['navColor']}}'></i> درحال انجام ({{$activesCount}})</span>
                </a>
            </li>
            <li>
                <a href="" class="" id="submit-done">
                    <span><i class='bx bxs-circle' style='color:{{Submit::mapSettings('done')['navColor']}}'></i> انجام شده ({{$doneCount}})</span>
                </a>
            </li>
        </ul>
    </nav>
</div>
