<x-main-layout page-title="Game">


      <div class="container">

        
        <x-question :country="$country" :current-question="$currentQuestion" :total-questions="$totalQuestions ?? 1"/>

        <div class="row">

          @foreach ($answers as $answer)
              <x-answer :capital="$answer"/>
          @endforeach

        </div>
        
    </div>

    <!-- cancel game -->
    <div class="text-center mt-5">
        <a href="{{route('home')}}" class="btn btn-outline-danger mt-3 px-5">CANCELAR JOGO</a>
    </div>

  
</x-main-layout>