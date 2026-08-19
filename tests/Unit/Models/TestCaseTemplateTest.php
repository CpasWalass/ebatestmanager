<?php

namespace Tests\Unit\Models;

use App\Models\TestCaseTemplate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TestCaseTemplateTest extends TestCase
{
    use RefreshDatabase;

    public function test_normalize_label_basic(): void
    {
        $this->assertEquals('termine', TestCaseTemplate::normalizeLabel('Terminé'));
        $this->assertEquals('valide', TestCaseTemplate::normalizeLabel('  VALIDÉ  '));
    }

    public function test_normalize_label_strips_special_chars(): void
    {
        $this->assertEquals('sousreserve', TestCaseTemplate::normalizeLabel('Sous réserve !'));
    }

    public function test_resolve_progress_value_exact_matches(): void
    {
        $this->assertEquals('a_faire', TestCaseTemplate::resolveProgressValue('À faire'));
        $this->assertEquals('en_cours', TestCaseTemplate::resolveProgressValue('En cours'));
        $this->assertEquals('bloque', TestCaseTemplate::resolveProgressValue('Bloqué'));
        $this->assertEquals('termine', TestCaseTemplate::resolveProgressValue('Terminé'));
    }

    public function test_resolve_progress_value_aliases(): void
    {
        $this->assertEquals('a_faire', TestCaseTemplate::resolveProgressValue('TODO'));
        $this->assertEquals('a_faire', TestCaseTemplate::resolveProgressValue('En attente'));
        $this->assertEquals('en_cours', TestCaseTemplate::resolveProgressValue('In Progress'));
        $this->assertEquals('termine', TestCaseTemplate::resolveProgressValue('DONE'));
        $this->assertEquals('termine', TestCaseTemplate::resolveProgressValue('OK'));
    }

    public function test_resolve_progress_value_unknown(): void
    {
        $this->assertNull(TestCaseTemplate::resolveProgressValue('foobar'));
    }

    public function test_resolve_verdict_value_exact_matches(): void
    {
        $this->assertEquals('valide', TestCaseTemplate::resolveVerdictValue('Validé'));
        $this->assertEquals('non_valide', TestCaseTemplate::resolveVerdictValue('Non validé'));
        $this->assertEquals('sous_reserve', TestCaseTemplate::resolveVerdictValue('Sous réserve'));
        $this->assertEquals('optimisation', TestCaseTemplate::resolveVerdictValue('Optimisation'));
    }

    public function test_resolve_verdict_value_aliases(): void
    {
        $this->assertEquals('valide', TestCaseTemplate::resolveVerdictValue('OK'));
        $this->assertEquals('valide', TestCaseTemplate::resolveVerdictValue('PASS'));
        $this->assertEquals('non_valide', TestCaseTemplate::resolveVerdictValue('FAIL'));
        $this->assertEquals('non_valide', TestCaseTemplate::resolveVerdictValue('KO'));
        $this->assertEquals('non_valide', TestCaseTemplate::resolveVerdictValue('Échec'));
    }

    public function test_resolve_verdict_value_unknown(): void
    {
        $this->assertNull(TestCaseTemplate::resolveVerdictValue('nonsense'));
    }

    public function test_resolve_nature_value_exact(): void
    {
        $this->assertEquals('Concluant', TestCaseTemplate::resolveNatureValue('Concluant'));
        $this->assertEquals('Erreurs Fonctionnelles', TestCaseTemplate::resolveNatureValue('Erreurs Fonctionnelles'));
    }

    public function test_resolve_nature_value_keyword(): void
    {
        $this->assertEquals('Erreurs Fonctionnelles', TestCaseTemplate::resolveNatureValue('BUG'));
        $this->assertEquals('Erreurs de Performance', TestCaseTemplate::resolveNatureValue('PERF'));
        $this->assertEquals('Erreurs Techniques', TestCaseTemplate::resolveNatureValue('technique'));
    }

    public function test_resolve_nature_value_substring(): void
    {
        $this->assertEquals('Erreurs Fonctionnelles', TestCaseTemplate::resolveNatureValue('fonctionnel'));
    }

    public function test_resolve_nature_value_empty(): void
    {
        $this->assertNull(TestCaseTemplate::resolveNatureValue(''));
    }

    public function test_default_fields_has_required_structure(): void
    {
        $fields = TestCaseTemplate::defaultFields();
        $this->assertIsArray($fields);
        $this->assertNotEmpty($fields);

        foreach ($fields as $field) {
            $this->assertArrayHasKey('name', $field);
            $this->assertArrayHasKey('label', $field);
            $this->assertArrayHasKey('type', $field);
        }
    }

    public function test_progress_options_structure(): void
    {
        $options = TestCaseTemplate::progressOptions();
        $this->assertArrayHasKey('a_faire', $options);
        $this->assertArrayHasKey('label', $options['a_faire']);
        $this->assertArrayHasKey('color', $options['a_faire']);
    }

    public function test_verdict_options_structure(): void
    {
        $options = TestCaseTemplate::verdictOptions();
        $this->assertArrayHasKey('valide', $options);
        $this->assertArrayHasKey('label', $options['valide']);
        $this->assertArrayHasKey('color', $options['valide']);
    }
}
