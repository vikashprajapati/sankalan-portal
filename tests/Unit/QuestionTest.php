<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Question;
use App\AnswerChoice;
use Illuminate\Support\Collection;

class QuestionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function question_has_many_options()
    {
        $question = create(Question::class);
        $options = create(AnswerChoice::class, 4, ['question_id' => $question->id]);

        tap($question->choices, function($relatedChoices) use ($options) {
            $this->assertInstanceOf(Collection::class, $relatedChoices);
            $this->assertCount(4, $relatedChoices);
            $this->assertSame($options->pluck('id')->toArray(), $relatedChoices->pluck('id')->toArray());
        });
    }
}
