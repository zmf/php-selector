<?php

include('selector.inc');

function test_selector($selector, $count) {
  global $html;
  $actual = count(select_elements($selector, $html));
  print  $actual == $count ? '.' : "\n '$selector' failed, expected $count but got $actual \n\n";
}

function test($selector, $expected) {
  $actual = selector_to_xpath($selector);
  print $actual == $expected ? '.' : "\n '$selector' \n    expected '$expected' \n    but got  '$actual'\n\n";
}

test('foo',                 'descendant-or-self::foo');
test('foo, bar',            'descendant-or-self::foo|descendant-or-self::bar');
test('foo bar',             'descendant-or-self::foo/descendant::bar');
test('foo    bar',          'descendant-or-self::foo/descendant::bar');
test('foo > bar',           'descendant-or-self::foo/bar');
test('foo >bar',            'descendant-or-self::foo/bar');
test('foo>bar',             'descendant-or-self::foo/bar');
test('foo> bar',            'descendant-or-self::foo/bar');
test('div#foo',             'descendant-or-self::div[@id="foo"]');
test('#foo',                'descendant-or-self::*[@id="foo"]');
test('div.foo',             'descendant-or-self::div[contains(concat(" ",@class," ")," foo ")]');
test('.foo',                'descendant-or-self::*[contains(concat(" ",@class," ")," foo ")]');
test('[id]',                'descendant-or-self::*[@id]');
test('[id=bar]',            'descendant-or-self::*[@id="bar"]');
test('foo[id=bar]',         'descendant-or-self::foo[@id="bar"]');
test(':button',             'descendant-or-self::input[@type="button"]');
test('textarea',            'descendant-or-self::textarea');
test(':submit',             'descendant-or-self::input[@type="submit"]');
test(':first-child',        'descendant-or-self::*/*[position()=1]');
test('div:first-child',     'descendant-or-self::*/div[position()=1]');
test(':last-child',         'descendant-or-self::*/*[position()=last()]');
test(':nth-last-child(2)',  'descendant-or-self::[position()=(last() - (2 - 1))]');
test('div:last-child',      'descendant-or-self::*/div[position()=last()]');
test(':nth-child(2)',       'descendant-or-self::*/*[position()=2]');
test('div:nth-child(2)',    'descendant-or-self::*/*[position()=2 and self::div]');
test('foo + bar',           'descendant-or-self::foo/following-sibling::bar[position()=1]');
test('li:contains(Foo)',    'descendant-or-self::li[contains(string(.),"Foo")]');

test('foo bar baz',         'descendant-or-self::foo/descendant::bar/descendant::baz');
test('foo + bar + baz',     'descendant-or-self::foo/following-sibling::bar[position()=1]/following-sibling::baz[position()=1]');
test('foo > bar > baz',     'descendant-or-self::foo/bar/baz');
test('p ~ p ~ p',           'descendant-or-self::p/following-sibling::p/following-sibling::p');
test('div#article p em',    'descendant-or-self::div[@id="article"]/descendant::p/descendant::em');
test('div.foo:first-child', 'descendant-or-self::div[contains(concat(" ",@class," ")," foo ")][position()=1]');
test('form#login > input[type=hidden]._method', 'descendant-or-self::form[@id="login"]/input[@type="hidden"][contains(concat(" ",@class," ")," _method ")]');

$html = <<<HTML
  <div id="article" class="block large">
    <h2>Article Name</h2>
    <p>Contents of article</p>
    <ul>
      <li class="a">One</li>
      <li class="bar">Two</li>
      <li class="bar a">Three</li>
      <li>Four</li>
      <li><a href="#">Five</a></li>
    </ul>
  </div>
HTML;

test_selector('*', 12);
test_selector('div', 1);
test_selector('div, p', 2);
test_selector('div , p', 2);
test_selector('div ,p', 2);
test_selector('div, p, ul li a', 3);
test_selector('div#article', 1);
test_selector('div#article.block', 1);
test_selector('div#article.large.block', 1);
test_selector('h2', 1);
test_selector('div h2', 1);
test_selector('div > h2', 1);
test_selector('ul li a', 1);
test_selector('ul > li > a', 1);
test_selector('a[href=#]', 1);
test_selector('a[href="#"]', 1);
test_selector('div[id="article"]', 1);
test_selector('h2:contains(Article)', 1);
test_selector('h2:contains(Article) + p', 1);
test_selector('h2:contains(Article) + p:contains(Contents)', 1);
test_selector('div p + ul', 1);
test_selector('li ~ li', 4);
test_selector('li ~ li ~ li', 3);
test_selector('li + li', 4);
test_selector('li + li + li', 3);
test_selector('li:first-child', 1);
test_selector('li:last-child', 1);
test_selector('li:contains(One):first-child', 1);
test_selector('li:nth-child(2)', 1);
test_selector('li:nth-child(3)', 1);
test_selector('li:nth-child(4)', 1);
test_selector('li:nth-child(6)', 0);
test_selector('li:nth-last-child(2)', 1);
test_selector('li:nth-last-child(3)', 1);
test_selector('li:nth-last-child(4)', 1);
test_selector('li:nth-last-child(6)', 0);
test_selector('.a', 2);
$dom = new SelectorDom($html);
print count($dom->select('a')) == 1 ? '.' : 'SelectorDOM failed';
print count($dom->select('ul li a')) == 1 ? '.' : 'SelectorDOM failed';
$divs = $dom->select('div');
print $divs[0]['attributes']['id'] == 'article' ? '.' : 'Attributes failed';

print "\n";
