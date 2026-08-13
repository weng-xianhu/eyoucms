// CodeMirror, copyright (c) by Marijn Haverbeke and others
// Distributed under an MIT license: https://codemirror.net/LICENSE

(function(mod) {
  if (typeof exports == "object" && typeof module == "object")
    mod(require("../../lib/codemirror"));
  else if (typeof define == "function" && define.amd)
    define(["../../lib/codemirror"], mod);
  else
    mod(CodeMirror);
})(function(CodeMirror) {
  "use strict";

  var Pos = CodeMirror.Pos;

  function SearchCursor(doc, query, pos, options) {
    this.atOccurrence = false;
    this.doc = doc;
    pos = pos ? doc.clipPos(pos) : Pos(0, 0);
    this.pos = {from: pos, to: pos};

    var caseFold;
    if (typeof options == "object") {
      caseFold = options.caseFold;
    } else {
      caseFold = options;
      options = null;
    }

    if (typeof query == "string") {
      if (caseFold == null) caseFold = false;
      this.matches = function(reverse, pos) {
        return (reverse ? searchStringBackward : searchStringForward)(doc, query, pos, caseFold);
      };
    } else {
      query = ensureFlags(query, "gm");
      if (!options || options.multiline !== false)
        this.matches = function(reverse, pos) {
          return (reverse ? searchRegexpBackwardMultiline : searchRegexpForwardMultiline)(doc, query, pos);
        };
      else
        this.matches = function(reverse, pos) {
          return (reverse ? searchRegexpBackward : searchRegexpForward)(doc, query, pos);
        };
    }
  }

  function ensureFlags(regexp, flags) {
    var current = regexp.flags, target = current;
    for (var i = 0; i < flags.length; i++) if (target.indexOf(flags.charAt(i)) == -1)
      target += flags.charAt(i);
    return current == target ? regexp : new RegExp(regexp.source, target);
  }

  function searchStringForward(doc, query, start, caseFold) {
    if (!query.length) return null;
    var searchFor = caseFold ? query.toLowerCase() : query;
    var line = start.line, ch = start.ch;
    for (; line < doc.lineCount(); line++, ch = 0) {
      var text = doc.getLine(line);
      if (caseFold) text = text.toLowerCase();
      var found = text.indexOf(searchFor, ch);
      if (found > -1) return {from: Pos(line, found), to: Pos(line, found + query.length)};
    }
  }

  function searchStringBackward(doc, query, start, caseFold) {
    if (!query.length) return null;
    var searchFor = caseFold ? query.toLowerCase() : query;
    for (var line = start.line, ch = start.ch; line >= 0; line--, ch = -1) {
      var text = doc.getLine(line);
      if (caseFold) text = text.toLowerCase();
      var found = text.lastIndexOf(searchFor, ch < 0 ? text.length : ch);
      if (found > -1) return {from: Pos(line, found), to: Pos(line, found + query.length)};
    }
  }

  function searchRegexpForward(doc, query, start) {
    query = ensureFlags(query, "g");
    for (var line = start.line, ch = start.ch; line < doc.lineCount(); line++, ch = 0) {
      query.lastIndex = ch;
      var text = doc.getLine(line), match = query.exec(text);
      if (match) return {from: Pos(line, match.index), to: Pos(line, match.index + match[0].length), match: match};
    }
  }

  function searchRegexpBackward(doc, query, start) {
    query = ensureFlags(query, "g");
    for (var line = start.line, ch = start.ch; line >= 0; line--, ch = -1) {
      var text = doc.getLine(line);
      if (ch > -1) text = text.slice(0, ch);
      var match = lastMatch(text, query);
      if (match) return {from: Pos(line, match.index), to: Pos(line, match.index + match[0].length), match: match};
    }
  }

  function lastMatch(string, query) {
    var match, prevMatch;
    while ((match = query.exec(string))) prevMatch = match;
    return prevMatch;
  }

  function searchRegexpForwardMultiline(doc, query, start) {
    query = ensureFlags(query, "gm");
    var string = "", chunk = 1, line = start.line, endLine = doc.lineCount();
    for (; line < endLine;) {
      for (var i = 0; i < chunk && line < endLine; i++) {
        var curLine = doc.getLine(line++);
        string = string ? string + "\n" + curLine : curLine;
      }
      chunk *= 2;
      query.lastIndex = start.ch;
      var match = query.exec(string);
      if (match) {
        var before = string.slice(0, match.index).split("\n"), inside = match[0].split("\n");
        var startLine = start.line + before.length - 1, startCh = before[before.length - 1].length;
        return {from: Pos(startLine, startCh),
                to: Pos(startLine + inside.length - 1,
                        inside.length == 1 ? startCh + inside[0].length : inside[inside.length - 1].length),
                match: match};
      }
    }
  }

  function searchRegexpBackwardMultiline(doc, query, start) {
    query = ensureFlags(query, "gm");
    var string, chunk = 1, line = start.line, firstLine = doc.firstLine();
    for (; line >= firstLine;) {
      for (var i = 0; i < chunk && line >= firstLine; i++) {
        var curLine = doc.getLine(line--);
        string = string ? curLine + "\n" + string : curLine;
      }
      chunk *= 2;
      var match = lastMatch(string, query);
      if (match) {
        var before = string.slice(0, match.index).split("\n"), inside = match[0].split("\n");
        var startLine = line + before.length, startCh = before[before.length - 1].length;
        return {from: Pos(startLine, startCh),
                to: Pos(startLine + inside.length - 1,
                        inside.length == 1 ? startCh + inside[0].length : inside[inside.length - 1].length),
                match: match};
      }
    }
  }

  SearchCursor.prototype = {
    findNext: function() {return this.find(false);},
    findPrevious: function() {return this.find(true);},

    find: function(reverse) {
      var result = this.matches(reverse, this.doc.clipPos(reverse ? this.pos.from : this.pos.to));
      while (result && CodeMirror.cmpPos(result.from, result.to) == 0) {
        if (reverse) {
          if (result.from.ch) result = this.matches(reverse, Pos(result.from.line, result.from.ch - 1));
          else if (result.from.line == this.doc.firstLine()) result = null;
          else result = this.matches(reverse, this.doc.clipPos(Pos(result.from.line - 1)));
        } else {
          if (result.to.ch < this.doc.getLine(result.to.line).length) result = this.matches(reverse, Pos(result.to.line, result.to.ch + 1));
          else if (result.to.line == this.doc.lastLine()) result = null;
          else result = this.matches(reverse, this.doc.clipPos(Pos(result.to.line + 1, 0)));
        }
      }

      if (result) {
        this.pos = result;
        this.atOccurrence = true;
        return this.pos.match || true;
      } else {
        var end = Pos(reverse ? this.doc.firstLine() : this.doc.lastLine() + 1, 0);
        this.pos = {from: end, to: end};
        return this.atOccurrence = false;
      }
    },

    from: function() {if (this.atOccurrence) return this.pos.from;},
    to: function() {if (this.atOccurrence) return this.pos.to;},

    replace: function(newText, origin) {
      if (!this.atOccurrence) return;
      var lines = CodeMirror.splitLines(newText);
      this.doc.replaceRange(lines, this.pos.from, this.pos.to, origin);
      this.pos.to = Pos(this.pos.from.line + lines.length - 1,
                        lines[lines.length - 1].length + (lines.length == 1 ? this.pos.from.ch : 0));
    }
  };

  CodeMirror.defineExtension("getSearchCursor", function(query, pos, caseFold) {
    return new SearchCursor(this.doc, query, pos, caseFold);
  });
  CodeMirror.defineDocExtension("getSearchCursor", function(query, pos, caseFold) {
    return new SearchCursor(this, query, pos, caseFold);
  });

  CodeMirror.defineExtension("selectMatches", function(query, caseFold) {
    var ranges = [];
    var cur = this.getSearchCursor(query, this.getCursor("from"), caseFold);
    while (cur.findNext()) {
      if (CodeMirror.cmpPos(cur.to(), this.getCursor("to")) > 0) break;
      ranges.push({anchor: cur.from(), head: cur.to()});
    }
    if (ranges.length)
      this.setSelections(ranges, 0);
  });
});
