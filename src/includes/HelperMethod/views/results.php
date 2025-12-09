<?php
use Tanuki\FormResult;
use Tanuki\HandlerResult;

/**
 * @var FormResult $formResult;
 */

function getHandlerName(HandlerResult $result): string {
  return preg_replace("/^.*\\\\([a-zA-Z0-9]+)$/", '$1', $result->getIdentifier());
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>Result Viewer</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, user-scalable=no">

  <style>
    * {
      margin: 0;
      padding: 0;
    }

    body {
      background-color: #000;
    }
    .result-view {
      max-width: 600px;
      margin: 20px auto 40px;
      padding: 20px;

      h1 {
        display: none;
        font-size: 22px;
        font-weight: normal;
        line-height: 1em;
      }

      > section {
        margin-top: 20px;

        h2 {
          padding: 10px 0;
          color: #fff;
          font-size: 18px;
          font-weight: normal;
          line-height: 1em;
        }

        dl {
          div {
            dt {
              padding: 20px;
              color: #fff;
              font-size: 20px;
              line-height: 1em;

              strong {
                display: block;
                font-weight: normal;
              }

              span {
                display: block;
                margin-top: 10px;
                font-size: 14px;
              }
            }

            dd {
              padding: 20px;
              background-color: #fff;

              pre {
                margin-top: 10px;
                padding: 20px;
                background-color: #f0f0f0;
              }
            }
          }

          .success {
            dt {
              background-color: #03b32cff;
            }
          }

          .failed {
            dt {
              background-color: #d32035ff;
            }
          }
        }
      }
    }
  </style>
</head>

<body>
  <div class="result-view">
    <h1>Results</h1>

    <section>
      <h2>PreHandlers</h2>

      <dl>
        <?php foreach($formResult->getPreHandlerResults() as $result): ?>
        <div class="<?php echo implode(' ', [$result->isSuccessful() ? 'success' : 'failed', $result->wasSkipped() ? 'skipped' : '']); ?>">
          <dt>
            <strong><?php echo getHandlerName($result); ?></strong>
            <span><?php echo $result->getIdentifier(); ?></span>
          </dt>
          <dd>
            <?php if($result->wasSkipped()): ?>
              <p>skipped.</p>
            <?php elseif($result->isSuccessful()): ?>
              <p>success.</p>
            <?php else: ?>
              <p><?php echo $result->getErrorMessage(); ?></p>
              <pre><code><?php var_dump($result->getData()); ?></code></pre>
            <?php endif;?>
          </dd>
        </div>
        <?php endforeach; ?>
      </dl>
    </section>

    <?php $validationErrors = $formResult->getValidationErrors(); ?>
    <?php if(count($validationErrors) > 0): ?>
    <section>
      <h2>Validation</h2>

      <dl>
        <?php foreach($validationErrors as $name => $fields): ?>
        <div class="failed">
          <dt><?php echo $name; ?></dt>
          <dd><?php echo implode(", ", $fields); ?></dd>
        </div>
        <?php endforeach; ?>
      </dl>
    </section>
    <?php endif; ?>

    <?php $postHandlerResults = $formResult->getPostHandlerResults(); ?>
    <?php if(count($postHandlerResults) > 0): ?>
    <section>
      <h2>PostHandlers</h2>

      <dl>
        <?php foreach($postHandlerResults as $result): ?>
        <div class="<?php echo implode(' ', [$result->isSuccessful() ? 'success' : 'failed', $result->wasSkipped() ? 'skipped' : '']); ?>">
          <dt>
            <strong><?php echo getHandlerName($result); ?></strong>
            <span><?php echo $result->getIdentifier(); ?></span>
          </dt>
          <dd>
            <?php if($result->wasSkipped()): ?>
              <p>skipped.</p>
            <?php elseif($result->isSuccessful()): ?>
              <p>success.</p>
            <?php else: ?>
              <p><?php echo $result->getErrorMessage(); ?></p>
              <pre><code><?php var_dump($result->getData()); ?></code></pre>
            <?php endif;?>
          </dd>
        </div>
        <?php endforeach; ?>
      </dl>
    </section>
    <?php endif; ?>
  </div>
</body>
</html>
