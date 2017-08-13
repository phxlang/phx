%pure_parser
%expect 0

%tokens

%%
start:
    optional_section_tokens section_rules optional_section_programs { $$ = \Phx\Yacc\Parser\Definition[$1, $2, $3]; }
;

optional_section_tokens:
      /* empty */                                                   { init(); }
    | token_list                                                    { $$ = $1; }
;

section_rules:
    T_DOUBLE_PERCENTAGE rule_group_list T_DOUBLE_PERCENTAGE         { $$ = $2; }
;

optional_section_programs:
    /* empty */                                                     { init(); }
;

action:
	  T_CURLY_OPEN T_STRING T_CURLY_CLOSE                           { $$ = \Phx\Yacc\Parser\Action[$2]; }
	| T_CURLY_OPEN T_CURLY_CLOSE                                    { $$ = \Phx\Yacc\Parser\Action[]; }
;

/* rule section */
rule:
      T_STRING action                                               { $$ = \Phx\Yacc\Parser\Rule[$1, $2]; }
    | action                                                        { $$ = \Phx\Yacc\Parser\Rule[null, $1]; }
    | T_STRING                                                      { $$ = \Phx\Yacc\Parser\Rule[$1, null]; }
;

rule_list:
      rule                                                          { init($1); }
    | rule_list T_PIPE rule                                         { push($1, $3); }
;

rule_group:
      T_STRING T_COLON rule_list T_SEMICOLON                        { $$ = \Phx\Yacc\Parser\RuleGroup[$1, $3]; }
;

rule_group_list:
       rule_group                                                   { init($1); }
     | rule_group_list rule_group                                   { push($1, $2); }
;

/* tokens section */
token:
       T_TOKEN tokens                                               { $$ = \Phx\Yacc\Parser\Token\Token[$2]; }
     | T_LEFT tokens                                                { $$ = \Phx\Yacc\Parser\Token\Left[$2]; }
     | T_RIGHT tokens                                               { $$ = \Phx\Yacc\Parser\Token\Right[$2]; }
     | T_NONASSOC tokens                                            { $$ = \Phx\Yacc\Parser\Token\NonAssoc[$2]; }
     | T_EXPECT T_NUM                                               { $$ = \Phx\Yacc\Parser\Expect[$2]; }
     | T_PURE_PARSER                                                { $$ = \Phx\Yacc\Parser\PureParser[]; }
;

token_name:
      T_STRING                                                      { $$ = $1; }
    | T_ENCAPSED_STRING                                             { $$ = $1; }
;

tokens:
      token_name                                                    { init($1); }
    | tokens token_name                                             { push($1, $2); }
;

token_list:
      token                                                         { init($1); }
    | token_list token                                              { push($1, $2); }
;

%%
