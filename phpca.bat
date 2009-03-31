@echo off
REM phpca
REM
REM Copyright (c) 2009 Stefan Priebsch <stefan@priebsch.de>
REM All rights reserved.
REM
REM Redistribution and use in source and binary forms, with or without modification,
REM are permitted provided that the following conditions are met:
REM
REM   * Redistributions of source code must retain the above copyright notice,
REM     this list of conditions and the following disclaimer.
REM
REM   * Redistributions in binary form must reproduce the above copyright notice,
REM     this list of conditions and the following disclaimer in the documentation
REM     and/or other materials provided with the distribution.
REM
REM   * Neither the name of Stefan Priebsch nor the names of contributors
REM     may be used to endorse or promote products derived from this software
REM     without specific prior written permission.
REM
REM THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
REM AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT REM NOT LIMITED TO,
REM THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR
REM PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER ORCONTRIBUTORS
REM BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY,
REM OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
REM SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
REM INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
REM CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
REM ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
REM POSSIBILITY OF SUCH DAMAGE.
REM

set PHPBIN="@php_bin@"
"@php_bin@" "@bin_dir@\phpca" %*
