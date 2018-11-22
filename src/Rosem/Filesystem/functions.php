<?php
$expectations = [
    ['bar', '../bar'],
    ['bar', './bar'],
    ['bar', '.././bar'],
    ['bar', '.././bar'],
    ['/foo/bar', '/foo/./bar'],
    ['/bar/', '/bar/./'],
    ['/', '/.'],
    ['/bar/', '/bar/.'],
    ['/bar', '/foo/../bar'],
    ['/', '/bar/../'],
    ['/', '/..'],
    ['/', '/bar/..'],
    ['/foo/', '/foo/bar/..'],
    ['', '.'],
    ['', '..'],
];
