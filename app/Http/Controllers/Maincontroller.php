<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class Maincontroller extends Controller
{
    
    private $app_data;


    public function home (){
        return view('home');
    }

    public function __construct(){

        //load app_data from app folder

        $this->app_data = require(app_path('app_data.php'));
    }

    public function prepareGame(Request $request){

        $request->validate([
            'total_questions' => 'required|integer|min:3|max:30'
        ] , 
    
    [
        'total_questions.required' => 'O número de questões é obrigatório',
        'total_questions.integer' => 'O número de questões tem que ser um valor inteiro',
        'total_questions.min' => 'O número minimo de questões é :min',
        'total_questions.max' => 'O número máximo de questões é :max'
    ]);

 
    //get total questions from the form
    $total_questions = intval($request->input('total_questions'));

    //prepare all the quiz structure
     $quiz = $this->prepareQuiz($total_questions);

     //sore the quiz in the session
     session()->put(
        ['quiz' => $quiz,
         'current_question' => $total_questions,
         'current_questions' =>1,
         'correct_answers' =>0,
         'wrong_answers' =>0
         ]
     );

     return redirect()->route('game');
    }

    private function prepareQuiz($total_questions)
    {
        $questions = [];

        // total de países vindo do app_data
        $total_countries = count($this->app_data);

        // cria índices embaralhados para perguntas únicas
        $indexes = range(0, $total_countries - 1);
        shuffle($indexes);
        $indexes = array_slice($indexes, 0, $total_questions);

        $question_number = 1;

        foreach ($indexes as $index) {
            $question = [
                'question_number' => $question_number++,
                'country' => $this->app_data[$index]['country'],
                'correct_answer' => $this->app_data[$index]['capital'],
            ];

            // cria respostas erradas
            $other_capitals = array_column($this->app_data, 'capital');

            // remove a resposta correta
            $other_capitals = array_diff($other_capitals, [$question['correct_answer']]);

            shuffle($other_capitals);

            // pega 3 erradas
            $question['wrong_answers'] = array_slice($other_capitals, 0, 3);

            // estado inicial da resposta
            $question['correct'] = null;

            $questions[] = $question;
        }

        return $questions;
    }

    public function game(){

        $quiz = session('quiz'); 
        $total_questions  = session('total_questions');
        $current_question  = session('current_question')-1;

        //prepare the answers to show in view

        $answers = $quiz[$current_question]['wrong_answers'];
        $answers[] = $quiz[$current_question]['correct_answer'];
        shuffle($answers);

        return view('game', [
            'country' => $quiz[$current_question]['country'],
            'totalQuestions' => $total_questions,
            'currentQuestion' => $current_question,
            'answers' => $answers
        ]);

    }


    public function answer($enc_answer){

        try {
            $answer = Crypt::decryptString($enc_answer); 
        } catch (\Exception $e) {
            return redirect()->route('game');
        }

        //game logic 
        $quiz = session('quiz');
        $current_question = session('current_question') -1;
        $correct_answer = $quiz[$current_question]['correct_answer'];
        $correct_answers = session('correct_answers');
        $wrong_answers = session('correct_answers');

        if($answer === $correct_answer){
            $correct_answers++;
            $quiz[$current_question]['correct'] = true;
        } else {
            $wrong_answers++;
            $quiz[$current_question]['correct'] = false;
        }

        //update session

        session()->put([
            'quiz' => $quiz,
            'correct_answers' => $correct_answers,
            'wrong_answers' => $wrong_answers,
        ]);

        //prepare data to show the correct answer
        $data = [
            'country' => $quiz[$current_question]['country'],
            'correct_answer' => $correct_answer,
            'choice_answer' => $answer,
            'currentQuestion' => $current_question ,
            'total_questions' => session('total_questions'),
        ];

        return view('answer_result', $data);
    }

    public function nextQuestion(){

        $current_question = session('current_question');
        $total_questions = session('total_questions');

        //check if game is over
        if($current_question < $total_questions){
            $current_question++;
            session()->put('current_question', $current_question);
            return redirect()->route('game');
        } else {

            //game over
            return redirect()->route('show_results');
        }
    }

    public function showResults(){

        // $correct_answers = session('correct_answers');
        // $wrong_answers = session('wrong_answers');
        // $total_questions = session('total_questions');

     
    }
}
