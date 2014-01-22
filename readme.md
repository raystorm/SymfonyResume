<h1>Symonfony Resume</h1>

This is a Website designed to display simple biographical info,
and Uploaded resumes.

**Features**
  - Upload resumes in a docx format
  - Leverages {PHPLiveDocX}(http://www.phplivedocx.org/articles/brief-introduction-to-phplivedocx/) to automatically convert uploaded resumes
    Uploaded Resumes converted to:
    + txt
    + html
    + pdf
  - because this is distributed online it is assumed Name/Address in the header,
    and the header is removed.
  - contact form (Feature Still in Development)
  - To simplify security concerns OpenId is used.
    Admin Access is granted via matching known email addresses in the *parameters.yml* file

