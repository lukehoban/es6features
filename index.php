<?php

namespace ApiBundle\Controller;

use Elastica;
use AppBundle\Entity\Adhesion;
use AppBundle\Entity\UserAccount;
use Swagger\Annotations as SWG;
use AppBundle\Entity\AdhesionState;
use ApiBundle\Form\Type\AdhesionType;
use AppBundle\Security\AdhesionVoter;
use FOS\RestBundle\Request\ParamFetcher;
use ApiBundle\Form\Type\AdhesionFileType;
use AppBundle\Service\AppMailer\AppMailer;
use ApiBundle\Form\Type\AdhesionBatchType;
use AppBundle\Repository\AdhesionRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Repository\AdhesionStateRepository;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\ElasticaBundle\Finder\PaginatedFinderInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class AdhesionController.
 *
 * @author Jesús Díaz <jdiaz@pagos360.com>
 */
class AdhesionController extends ApiController
{
    /**
     * @SWG\Path(
     *      path="/adhesion",
     *      @SWG\Options(
     *          tags={"CORS"},
     *          @SWG\Response(
     *              response=200,
     *              description="Ok",
     *              @SWG\Header(
     *                  type="string",
     *                  header="Access-Control-Allow-Headers"
     *              ),
     *              @SWG\Header(
     *                  type="string",
     *                  header="Access-Control-Allow-Methods"
     *              ),
     *              @SWG\Header(
     *                  type="string",
     *                  header="Access-Control-Allow-Origin"
     *              )
     *          ),
     *          x={"amazon-apigateway-integration": {
     *              "type": "mock",
     *              "requestTemplates": {
     *                  "application/json": "{'statusCode': 200}"
     *              },
     *              "responses": {
     *                  "default": {
     *                      "statusCode": "200",
     *                      "responseParameters": {
     *                          "method.response.header.Access-Control-Allow-Headers": "'Content-Type,X-Amz-Date,Authorization,X-Api-Key'",
     *                          "method.response.header.Access-Control-Allow-Methods": "'GET,POST'",
     *                          "method.response.header.Access-Control-Allow-Origin": "'*'"
     *                      }
     *                  }
     *              }
     *          }}
     *      )
     * )
     */

    /**
     * Lists all adhesions.
     *
     * @FOSRest\Get(
     *      "/adhesion",
     *      name="api_adhesion_list",
     *      options={"method_prefix"=false}
     * )
     *
     * @SWG\Path(
     *      path="/adhesion",
     *      @SWG\Get(
     *          tags={"Adhesion"},
     *          summary="Lists all adhesions",
     *          @SWG\Parameter(
     *              in="header",
     *              type="string",
     *              required=true,
     *              name="Authorization"
     *          ),
     *          @SWG\Parameter(
     *              in="query",
     *              type="string",
     *              required=false,
     *              name="account"
     *          ),
     *          @SWG\Parameter(
     *              in="query",
     *              type="integer",
     *              required=false,
     *              name="limit"
     *          ),
     *          @SWG\Parameter(
     *              in="query",
     *              type="integer",
     *              required=false,
     *              name="page"
     *          ),
     *          @SWG\Parameter(
     *              in="query",
     *              type="integer",
     *              required=false,
     *              name="q"
     *          ),
     *          @SWG\Parameter(
     *              in="query",
     *              type="integer",
     *              required=false,
     *              name="state"
     *          ),
     *          @SWG\Response(
     *              response=200,
     *              description="Ok",
     *              @SWG\Header(
     *                  type="string",
     *                  header="Access-Control-Allow-Headers"
     *              ),
     *              @SWG\Header(
     *                  type="string",
     *                  header="Access-Control-Allow-Methods"
     *              ),
     *              @SWG\Header(
     *                  type="string",
     *                  header="Access-Control-Allow-Origin"
     *              ),
     *              @SWG\Property(
     *                  @SWG\Items(ref="#/definitions/Adhesion")
     *              )
     *          ),
     *          @SWG\Response(
     *              response=401,
     *              description="Unauthorized",
     *              @SWG\Header(
     *                  type="string",
     *                  header="Access-Control-Allow-Headers"
     *              ),
     *              @SWG\Header(
     *                  type="string",
     *                  header="Access-Control-Allow-Methods"
     *              ),
     *              @SWG\Header(
     *                  type="string",
     *                  header="Access-Control-Allow-Origin"
     *              )
     *          ),
     *          @SWG\Response(
     *              response=403,
     *              description="Forbidden",
     *              @SWG\Header(
     *                  type="string",
     *                  header="Access-Control-Allow-Headers"
     *              ),
     *              @SWG\Header(
     *                  type="string",
     *                  header="Access-Control-Allow-Methods"
     *              ),
     *              @SWG\Header(
     *                  type="string",
     *                  header="Access-Control-Allow-Origin"
     *              )
     *          ),
     *          x={"amazon-apigateway-integration": {
     *              "requestParameters" : {
     *                  "integration.request.header.Authorization": "method.request.header.Authorization",
     *                  "integration.request.querystring.account": "method.request.querystring.account",
     *                  "integration.request.querystring.limit": "method.request.querystring.limit",
     *                  "integration.request.querystring.page": "method.request.querystring.page",
     *                  "integration.request.querystring.q": "method.request.querystring.q",
     *                  "integration.request.querystring.state": "method.request.querystring.state"
     *              },
     *              "responses": {
     *                  "default": {
     *                      "statusCode": "200",
     *                      "responseParameters": {
     *                          "method.response.header.Access-Control-Allow-Headers": "'Content-Type,X-Amz-Date,Authorization,X-Api-Key'",
     *                          "method.response.header.Access-Control-Allow-Methods": "'GET'",
     *                          "method.response.header.Access-Control-Allow-Origin": "'*'"
     *                      }
     *                  },
     *                  "401": {
     *                      "statusCode": "401",
     *                      "responseParameters": {
     *                          "method.response.header.Access-Control-Allow-Headers": "'Content-Type,X-Amz-Date,Authorization,X-Api-Key'",
     *                          "method.response.header.Access-Control-Allow-Methods": "'GET'",
     *                          "method.response.header.Access-Control-Allow-Origin": "'*'"
     *                      }
     *                  },
     *                  "403": {
     *                      "statusCode": "403",
     *                      "responseParameters": {
     *                          "method.response.header.Access-Control-Allow-Headers": "'Content-Type,X-Amz-Date,Authorization,X-Api-Key'",
     *                          "method.response.header.Access-Control-Allow-Methods": "'GET'",
     *                          "method.response.header.Access-Control-Allow-Origin": "'*'"
     *                      }
     *                  }
     *              },
     *              "uri": "https://${stageVariables.host}/api/adhesion",
     *              "passthroughBehavior": "when_no_match",
     *              "httpMethod": "GET",
     *              "type": "http"
     *          }}
     *      )
     * )
     *
     * @QueryParam(name="account", nullable=true, description="Secondary user account Id")
     * @QueryParam(name="limit", default=20, requirements="\d+", strict=true, description="Max number of results")
     * @QueryParam(name="page", default=1, requirements="\d+", strict=true, description="Number of page")
     * @QueryParam(name="state", default="", description="Current state")
     * @QueryParam(name="q", allowBlank=true, nullable=false, description="Search by query string")
     *
     * @param ParamFetcher $paramFetcher
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAction(ParamFetcher $paramFetcher)
    {
        $q = $paramFetcher->get('q');
        $page = $paramFetcher->get('page');
        $limit = $paramFetcher->get('limit');
        $state = $paramFetcher->get('state');
        $userAccountId = $paramFetcher->get('account');

        /** @var UserAccount $userAccount */
        $userAccount = $this->get('app.service.user_account_fetcher')->fetch($userAccountId);

        if (!$userAccount) {
            return $this->createForbiddenView();
        }

        // Gets user account
        $userAccountId = $userAccount->getUserAccountId();

        /** @var PaginatedFinderInterface $finder */
        $finder = $this->get('fos_elastica.finder.adhesions.adhesion');

        $query = new Elastica\Query([
            'query' => [
                'bool' => [
                    'must' => [
                        [
                            'query_string' => [
                                'query' => "*$q*"
                            ]
                        ],
                        [
                            'match' => [
                                'state.name' => [
                                    'query' => $state,
                                    'zero_terms_query' => 'all'
                                ]
                            ]
                        ],
                        [
                            'match' => [
                                'user_account.user_account_id' => [
                                    'query' => $userAccountId
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            'sort' => [
                'created_at' => 'desc'
            ]
        ]);

        return $this->createOkElasticaPaginatedView($query, $finder, $page, $limit);
    }

    /**
     * Creates an adhesion.
     *
     * @FOSRest\Post(
     *      "/adhesion",
     *      name="api_adhesion_create",
     *      options={"method_prefix"=false}
     * )
     *
     * @SWG\Path(
     *      path="/adhesion",
     *      @SWG\Post(
     *          tags={"Adhesion"},
     *          summary="Creates a new adhesion",
     *          @SWG\Parameter(
     *              in="header",
     *              type="string",
     *              required=true,
     *              name="Authorization"
     *          ),
     *          @SWG\Parameter(
     *              in="query",
     *              type="string",
     *              required=false,
     *              name="account"
     *          ),
     *          @SWG\Response(
     *              response=201,
     *              description="Created",
     *              @SWG\Header(
     *                  type="string",
     *                  header="Access-Control-Allow-Headers"
     *              ),
     *              @SWG\Header(
     *                  type="string",
     *                  header="Access-Control-Allow-Methods"
     *              ),
     *              @SWG\Header(
     *                  type="string",
     *                  header="Access-Control-Allow-Origin"
     *              ),
     *              @SWG\Schema(
     *                  @SWG\Items(ref="#/definitions/Adhesion")
     *              )
     *          ),
     *          @SWG\Response(
     *              response=400,
     *              description="Bad Request",
     *              @SWG\Header(
     *                  type="string",
     *                  header="Access-Control-Allow-Headers"
     *              ),
     *              @SWG\Header(
     *                  type="string",
     *                  header="Access-Control-Allow-Methods"
     *              ),
     *              @SWG\Header(
     *                  type="string",
     *                  header="Access-Control-Allow-Origin"
     *              )
     *          ),
     *          @SWG\Response(
     *              response=401,
     *              description="Unauthorized",
     *              @SWG\Header(
     *                  type="string",
     *                  header="Access-Control-Allow-Headers"
     *              ),
     *              @SWG\Header(
     *                  type="string",
     *                  header="Access-Control-Allow-Methods"
     *              ),
     *              @SWG\Header(
     *                  type="string",
     *                  header="Access-Control-Allow-Origin"
     *              )
     *          ),
     *          @SWG\Response(
     *              response=403,
     *              description="Forbidden",
     *              @SWG\Header(
     *                  type="string",
     *                  header="Access-Control-Allow-Headers"
     *              ),
     *              @SWG\Header(
     *                  type="string",
     *                  header="Access-Control-Allow-Methods"
     *              ),
     *              @SWG\Header(
     *                  type="string",
     *                  header="Access-Control-Allow-Origin"
     *              )
     *          ),
     *          x={"amazon-apigateway-integration": {
     *              "requestParameters" : {
     *                  "integration.request.header.Authorization": "method.request.header.Authorization",
     *                  "integration.request.querystring.account": "method.request.querystring.account"
     *              },
     *              "responses": {
     *                  "default": {
     *                      "statusCode": "201",
     *                      "responseParameters": {
     *                          "method.response.header.Access-Control-Allow-Headers": "'Content-Type,X-Amz-Date,Authorization,X-Api-Key'",
     *                          "method.response.header.Access-Control-Allow-Methods": "'POST'",
     *                          "method.response.header.Access-Control-Allow-Origin": "'*'"
     *                      }
     *                  },
     *                  "400": {
     *                      "statusCode": "400",
     *                      "responseParameters": {
     *                          "method.response.header.Access-Control-Allow-Headers": "'Content-Type,X-Amz-Date,Authorization,X-Api-Key'",
     *                          "method.response.header.Access-Control-Allow-Methods": "'POST'",
     *                          "method.response.header.Access-Control-Allow-Origin": "'*'"
     *                      }
     *                  },
     *                  "401": {
     *                      "statusCode": "401",
     *                      "responseParameters": {
     *                          "method.response.header.Access-Control-Allow-Headers": "'Content-Type,X-Amz-Date,Authorization,X-Api-Key'",
     *                          "method.response.header.Access-Control-Allow-Methods": "'POST'",
     *                          "method.response.header.Access-Control-Allow-Origin": "'*'"
     *                      }
     *                  },
     *                  "403": {
     *                      "statusCode": "403",
     *                      "responseParameters": {
     *                          "method.response.header.Access-Control-Allow-Headers": "'Content-Type,X-Amz-Date,Authorization,X-Api-Key'",
     *                          "method.response.header.Access-Control-Allow-Methods": "'POST'",
     *                          "method.response.header.Access-Control-Allow-Origin": "'*'"
     *                      }
     *                  },
     *              },
     *              "uri": "https://${stageVariables.host}/api/adhesion",
     *              "passthroughBehavior": "when_no_match",
     *              "httpMethod": "POST",
     *              "type": "http"
     *          }}
     *      )
     * )
     *
     * @QueryParam(name="account", nullable=true, description="Secondary user account Id")
     *
     * @param Request $request
     * @param ParamFetcher $paramFetcher
     *
     * @return Response
     */
    public function createAction(Request $request, ParamFetcher $paramFetcher)
    {
        $userAccountId = $paramFetcher->get('account');
        $userAccount = $this->get('app.service.user_account_fetcher')->fetch($userAccountId);

        if (!$userAccount) {
            return $this->createForbiddenView();
        }

        $adhesion = new Adhesion();

        /** @var AdhesionState $pendingToSignState */
        $state = $this->getDoctrine()
            ->getRepository('AppBundle:AdhesionState')
            ->find(AdhesionStateRepository::PENDING_TO_SIGN);

        $form = $this->createForm(AdhesionType::class, $adhesion, [
            'state' => $state,
            'user_account' => $userAccount,
            'validation_groups' => ['create'],
        ]);

        $form->handleRequest($request);

        $this->denyAccessUnlessGranted(AdhesionVoter::CREATE_ACTION, $adhesion);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($adhesion);
            $em->flush();

            /** @var AppMailer $appMailer */
            $appMailer = $this->get('app.mailer');
            $adhesionMailer = $appMailer->getAdhesionMailer();

            $signingToken = $adhesion->getSigningToken();
            $uri = 'http:'.$this->generateUrl('user_action_adhesion_sign', ['signingToken' => $signingToken], UrlGeneratorInterface::NETWORK_PATH);
            $adhesionMailer->sendSignatureRequest($adhesion, $uri);

            return $this->createCreatedView($adhesion);
        }

        return $this->createBadRequestView($form);
    }

    /**
     * @SWG\Path(
     *      path="/adhesion/{id}",
     *      @SWG\Options(
     *          tags={"CORS"},
     *          @SWG\Response(
     *              response=200,
     *              description="Ok",
     *              @SWG\Header(
     *                  type="string",
     *                  header="Access-Control-Allow-Headers"
     *              ),
     *              @SWG\Header(
     *                  type="string",
     *                  header="Access-Control-Allow-Methods"
     *              ),
     *              @SWG\Header(
     *                  type="string",
     *                  header="Access-Control-Allow-Origin"
     *              )
     *          ),
     *          x={"amazon-apigateway-integration": {
     *              "type": "mock",
     *              "requestTemplates": {
     *                  "application/json": "{'statusCode': 200}"
     *              },
     *              "responses": {
     *                  "default": {
     *                      "statusCode": "200",
     *                      "responseParameters": {
     *                          "method.response.header.Access-Control-Allow-Headers": "'Content-Type,X-Amz-Date,Authorization,X-Api-Key'",
     *                          "method.response.header.Access-Control-Allow-Methods": "'GET'",
     *                          "method.response.header.Access-Control-Allow-Origin": "'*'"
     *                      }
     *                  }
     *              }
     *          }}
     *      )
     * )
     */

    /**
     * Retrieves an adhesion.
     *
     * @FOSRest\Get(
     *      "/adhesion/{id}",
     *      name="api_adhesion_retrieve",
     *      options={"method_prefix"=false}
     * )
     *
     * @SWG\Path(
     *      path="/adhesion/{id}",
     *      @SWG\Get(
     *          tags={"Adhesion"},
     *          summary="Retrieves an adhesion",
     *          @SWG\Parameter(
     *              in="header",
     *              type="string",
     *              required=true,
     *              name="Authorization"
     *          ),
     *          @SWG\Parameter(
     *              in="path",
     *              type="integer",
     *              required=true,
     *              name="id"
     *          ),
     *          @SWG\Parameter(
     *              in="query",
     *              type="string",
     *              required=false,
     *              name="account"
     *          ),
     *          @SWG\Response(
     *              response=200,
     *              description="Ok",
     *              @SWG\Header(
     *                  type="string",
     *                  header="Access-Control-Allow-Headers"
     *              ),
     *              @SWG\Header(
     *                  type="string",
     *                  header="Access-Control-Allow-Methods"
     *              ),
     *              @SWG\Header(
     *                  type="string",
     *                  header="Access-Control-Allow-Origin"
     *              ),
     *              @SWG\Schema(
     *                  @SWG\Items(ref="#/definitions/Adhesion")
     *              )
     *          ),
     *          @SWG\Response(
     *              response=401,
     *              description="Unauthorized",
     *              @SWG\Header(
     *                  type="string",
     *                  header="Access-Control-Allow-Headers"
     *              ),
     *              @SWG\Header(
     *                  type="string",
     *                  header="Access-Control-Allow-Methods"
     *              ),
     *              @SWG\Header(
     *                  type="string",
     *                  header="Access-Control-Allow-Origin"
     *              )
     *          ),
     *          @SWG\Response(
     *              response=403,
     *              description="Forbidden",
     *              @SWG\Header(
     *                  type="string",
     *                  header="Access-Control-Allow-Headers"
     *              ),
     *              @SWG\Header(
     *                  type="string",
     *                  header="Access-Control-Allow-Methods"
     *              ),
     *              @SWG\Header(
     *                  type="string",
     *                  header="Access-Control-Allow-Origin"
     *              )
     *          ),
     *          @SWG\Response(
     *              response=404,
     *              description="Not Found",
     *              @SWG\Header(
     *                  type="string",
     *                  header="Access-Control-Allow-Headers"
     *              ),
     *              @SWG\Header(
     *                  type="string",
     *                  header="Access-Control-Allow-Methods"
     *              ),
     *              @SWG\Header(
     *                  type="string",
     *                  header="Access-Control-Allow-Origin"
     *              )
     *          ),
     *          x={"amazon-apigateway-integration": {
     *              "requestParameters" : {
     *                  "integration.request.header.Authorization": "method.request.header.Authorization",
     *                  "integration.request.querystring.account": "method.request.querystring.account",
     *                  "integration.request.path.id": "method.request.path.id"
     *              },
     *              "responses": {
     *                  "200": {
     *                      "statusCode": "200",
     *                      "responseParameters": {
     *                          "method.response.header.Access-Control-Allow-Headers": "'Content-Type,X-Amz-Date,Authorization,X-Api-Key'",
     *                          "method.response.header.Access-Control-Allow-Methods": "'GET'",
     *                          "method.response.header.Access-Control-Allow-Origin": "'*'"
     *                      }
     *                  },
     *                  "401": {
     *                      "statusCode": "401",
     *                      "responseParameters": {
     *                          "method.response.header.Access-Control-Allow-Headers": "'Content-Type,X-Amz-Date,Authorization,X-Api-Key'",
     *                          "method.response.header.Access-Control-Allow-Methods": "'GET'",
     *                          "method.response.header.Access-Control-Allow-Origin": "'*'"
     *                      }
     *                  },
     *                  "403": {
     *                      "statusCode": "403",
     *                      "responseParameters": {
     *                          "method.response.header.Access-Control-Allow-Headers": "'Content-Type,X-Amz-Date,Authorization,X-Api-Key'",
     *                          "method.response.header.Access-Control-Allow-Methods": "'GET'",
     *                          "method.response.header.Access-Control-Allow-Origin": "'*'"
     *                      }
     *                  },
     *                  "404": {
     *                      "statusCode": "404",
     *                      "responseParameters": {
     *                          "method.response.header.Access-Control-Allow-Headers": "'Content-Type,X-Amz-Date,Authorization,X-Api-Key'",
     *                          "method.response.header.Access-Control-Allow-Methods": "'GET'",
     *                          "method.response.header.Access-Control-Allow-Origin": "'*'"
     *                      }
     *                  }
     *              },
     *              "uri": "https://${stageVariables.host}/api/adhesion/{id}",
     *              "passthroughBehavior": "when_no_match",
     *              "httpMethod": "GET",
     *              "type": "http"
     *          }}
     *      )
     * )
     *
     * @QueryParam(name="account", nullable=true, description="Secondary user account Id")
     *
     * @param $id
     * @param ParamFetcher $paramFetcher
     *
     * @return Response
     */
    public function retrieveAction($id, ParamFetcher $paramFetcher)
    {
        $userAccountId = $paramFetcher->get('account');
        $userAccount = $this->get('app.service.user_account_fetcher')->fetch($userAccountId);

        if (!$userAccount) {
            return $this->createForbiddenView();
        }

        $adhesion = $this->getDoctrine()
            ->getRepository('AppBundle:Adhesion')
            ->findOneBy(['id' => $id, 'userAccount' => $userAccount]);

        if (!$adhesion) {
            return $this->createNotFoundView();
        }

        $this->denyAccessUnlessGranted(AdhesionVoter::VIEW_ACTION, $adhesion);

        return $this->createOkView($adhesion);
    }

    /**
     * @SWG\Path(
     *      path="/adhesion/{id}/cancel",
     *      @SWG\Options(
     *          tags={"CORS"},
     *          @SWG\Response(
     *              response=200,
     *              description="Ok",
     *              @SWG\Header(
     *                  type="string",
     *                  header="Access-Control-Allow-Headers"
     *              ),
     *              @SWG\Header(
     *                  type="string",
     *                  header="Access-Control-Allow-Methods"
     *              ),
     *              @SWG\Header(
     *                  type="string",
     *                  header="Access-Control-Allow-Origin"
     *              )
     *          ),
     *          x={"amazon-apigateway-integration": {
     *              "type": "mock",
     *              "requestTemplates": {
     *                  "application/json": "{'statusCode': 200}"
     *              },
     *              "responses": {
     *                  "default": {
     *                      "statusCode": "200",
     *                      "responseParameters": {
     *                          "method.response.header.Access-Control-Allow-Headers": "'Content-Type,X-Amz-Date,Authorization,X-Api-Key'",
     *                          "method.response.header.Access-Control-Allow-Methods": "'PUT'",
     *                          "method.response.header.Access-Control-Allow-Origin": "'*'"
     *                      }
     *                  }
     *              }
     *          }}
     *      )
     * )
     */

    /**
     * Cancels an adhesion.
     *
     * @FOSRest\Put(
     *      "/adhesion/{id}/cancel",
     *      name="api_adhesion_cancel",
     *      options={"method_prefix"=false}
     * )
     *
     * @SWG\Path(
     *      path="/adhesion/{id}/cancel",
     *      @SWG\Put(
     *          tags={"Adhesion"},
     *          summary="Cancels an adhesion",
     *          @SWG\Parameter(
     *              in="header",
     *              type="string",
     *              required=true,
     *              name="Authorization"
     *          ),
     *          @SWG\Parameter(
     *              in="path",
     *              type="integer",
     *              required=true,
     *              name="id"
     *          ),
     *          @SWG\Parameter(
     *              in="query",
     *              type="string",
     *              required=false,
     *              name="account"
     *          ),
     *          @SWG\Response(
     *              response=200,
     *              description="Ok",
     *              @SWG\Header(
     *                  type="string",
     *                  header="Access-Control-Allow-Headers"
     *              ),
     *              @SWG\Header(
     *                  type="string",
     *                  header="Access-Control-Allow-Methods"
     *              ),
     *              @SWG\Header(
     *                  type="string",
     *                  header="Access-Control-Allow-Origin"
     *              ),
     *              @SWG\Schema(
     *                  @SWG\Items(ref="#/definitions/Adhesion")
     *              )
     *          ),
     *          @SWG\Response(
     *              response=400,
     *              description="Bad Request",
     *              @SWG\Header(
     *                  type="string",
     *                  header="Access-Control-Allow-Headers"
     *              ),
     *              @SWG\Header(
     *                  type="string",
     *                  header="Access-Control-Allow-Methods"
     *              ),
     *              @SWG\Header(
     *                  type="string",
     *                  header="Access-Control-Allow-Origin"
     *              )
     *          ),
     *          @SWG\Response(
     *              response=401,
     *              description="Unauthorized",
     *              @SWG\Header(
     *                  type="string",
     *                  header="Access-Control-Allow-Headers"
     *              ),
     *              @SWG\Header(
     *                  type="string",
     *                  header="Access-Control-Allow-Methods"
     *              ),
     *              @SWG\Header(
     *                  type="string",
     *                  header="Access-Control-Allow-Origin"
     *              )
     *          ),
     *          @SWG\Response(
     *              response=403,
     *              description="Forbidden",
     *              @SWG\Header(
     *                  type="string",
     *                  header="Access-Control-Allow-Headers"
     *              ),
     *              @SWG\Header(
     *                  type="string",
     *                  header="Access-Control-Allow-Methods"
     *              ),
     *              @SWG\Header(
     *                  type="string",
     *                  header="Access-Control-Allow-Origin"
     *              )
     *          ),
     *          @SWG\Response(
     *              response=404,
     *              description="Not Found",
     *              @SWG\Header(
     *                  type="string",
     *                  header="Access-Control-Allow-Headers"
     *              ),
     *              @SWG\Header(
     *                  type="string",
     *                  header="Access-Control-Allow-Methods"
     *              ),
     *              @SWG\Header(
     *                  type="string",
     *                  header="Access-Control-Allow-Origin"
     *              )
     *          ),
     *          x={"amazon-apigateway-integration": {
     *              "requestParameters" : {
     *                  "integration.request.header.Authorization": "method.request.header.Authorization",
     *                  "integration.request.querystring.account": "method.request.querystring.account",
     *                  "integration.request.path.id": "method.request.path.id"
     *              },
     *              "responses": {
     *                  "200": {
     *                      "statusCode": "200",
     *                      "responseParameters": {
     *                          "method.response.header.Access-Control-Allow-Headers": "'Content-Type,X-Amz-Date,Authorization,X-Api-Key'",
     *                          "method.response.header.Access-Control-Allow-Methods": "'PUT'",
     *                          "method.response.header.Access-Control-Allow-Origin": "'*'"
     *                      }
     *                  },
     *                  "401": {
     *                      "statusCode": "401",
     *                      "responseParameters": {
     *                          "method.response.header.Access-Control-Allow-Headers": "'Content-Type,X-Amz-Date,Authorization,X-Api-Key'",
     *                          "method.response.header.Access-Control-Allow-Methods": "'PUT'",
     *                          "method.response.header.Access-Control-Allow-Origin": "'*'"
     *                      }
     *                  },
     *                  "403": {
     *                      "statusCode": "403",
     *                      "responseParameters": {
     *                          "method.response.header.Access-Control-Allow-Headers": "'Content-Type,X-Amz-Date,Authorization,X-Api-Key'",
     *                          "method.response.header.Access-Control-Allow-Methods": "'PUT'",
     *                          "method.response.header.Access-Control-Allow-Origin": "'*'"
     *                      }
     *                  },
     *                  "404": {
     *                      "statusCode": "404",
     *                      "responseParameters": {
     *                          "method.response.header.Access-Control-Allow-Headers": "'Content-Type,X-Amz-Date,Authorization,X-Api-Key'",
     *                          "method.response.header.Access-Control-Allow-Methods": "'PUT'",
     *                          "method.response.header.Access-Control-Allow-Origin": "'*'"
     *                      }
     *                  }
     *              },
     *              "uri": "https://${stageVariables.host}/api/adhesion/{id}/cancel",
     *              "passthroughBehavior": "when_no_match",
     *              "httpMethod": "PUT",
     *              "type": "http"
     *          }}
     *      )
     * )
     *
     * @QueryParam(name="account", nullable=true, description="Secondary user account Id")
     *
     * @param $id
     * @param ParamFetcher $paramFetcher
     *
     * @return Response
     */
    public function cancelAction(ParamFetcher $paramFetcher, $id)
    {
        $userAccountId = $paramFetcher->get('account');
        $userAccount = $this->get('app.service.user_account_fetcher')->fetch($userAccountId);

        if (!$userAccount) {
            return $this->createForbiddenView();
        }

        /** @var Adhesion $adhesion */
        $adhesion = $this->getDoctrine()
            ->getRepository('AppBundle:Adhesion')
            ->findOneBy(['id' => $id, 'userAccount' => $userAccount]);

        if (!$adhesion) {
            return $this->createNotFoundView();
        }

        $this->denyAccessUnlessGranted(AdhesionVoter::CANCEL_ACTION, $adhesion);

        /** @var AdhesionState $adhesionCanceledState */
        $adhesionCanceledState = $this->getDoctrine()
            ->getRepository('AppBundle:AdhesionState')
            ->find(AdhesionStateRepository::CANCELED);

        $adhesion->setState($adhesionCanceledState);
        $validationErrors = $this->get('validator')->validate($adhesion, null, ['cancel']);

        if (0 === count($validationErrors)) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($adhesion);
            $em->flush();

            return $this->createOkView($adhesion);
        }

        return $this->createBadRequestView($validationErrors);
    }

    /**
     * @SWG\Path(
     *      path="/adhesion/unique/{itemId}/{externalReference}",
     *      @SWG\Options(
     *          tags={"CORS"},
     *          @SWG\Response(
     *              response=200,
     *              description="Ok",
     *              @SWG\Header(
     *                  type="string",
     *                  header="Access-Control-Allow-Headers"
     *              ),
     *              @SWG\Header(
     *                  type="string",
     *                  header="Access-Control-Allow-Methods"
     *              ),
     *              @SWG\Header(
     *                  type="string",
     *                  header="Access-Control-Allow-Origin"
     *              )
     *          ),
     *          x={"amazon-apigateway-integration": {
     *              "type": "mock",
     *              "requestTemplates": {
     *                  "application/json": "{'statusCode': 200}"
     *              },
     *              "responses": {
     *                  "default": {
     *                      "statusCode": "200",
     *                      "responseParameters": {
     *                          "method.response.header.Access-Control-Allow-Headers": "'Content-Type,X-Amz-Date,Authorization,X-Api-Key'",
     *                          "method.response.header.Access-Control-Allow-Methods": "'GET'",
     *                          "method.response.header.Access-Control-Allow-Origin": "'*'"
     *                      }
     *                  }
     *              }
     *          }}
     *      )
     * )
     */

    /**
     * Checks if the adhesion exits.
     *
     * @FOSRest\Get(
     *      "/adhesion/unique/{itemId}/{externalReference}",
     *      name="api_adhesion_is_unique",
     *      options={"method_prefix"=false}
     * )
     *
     * @SWG\Path(
     *      path="/adhesion/unique/{itemId}/{externalReference}",
     *      @SWG\Get(
     *          tags={"Adhesion"},
     *          summary="Checks if the adhesion exits",
     *          @SWG\Parameter(
     *              in="header",
     *              type="string",
     *              required=true,
     *              name="Authorization"
     *          ),
     *          @SWG\Parameter(
     *              in="path",
     *              type="integer",
     *              required=true,
     *              name="itemId"
     *          ),
     *          @SWG\Parameter(
     *              in="path",
     *              type="string",
     *              required=true,
     *              name="externalReference"
     *          ),
     *          @SWG\Response(
     *              response=200,
     *              description="Ok",
     *              @SWG\Header(
     *                  type="string",
     *                  header="Access-Control-Allow-Headers"
     *              ),
     *              @SWG\Header(
     *                  type="string",
     *                  header="Access-Control-Allow-Methods"
     *              ),
     *              @SWG\Header(
     *                  type="string",
     *                  header="Access-Control-Allow-Origin"
     *              ),
     *              @SWG\Schema(
     *                  @SWG\Items(ref="#/definitions/Adhesion")
     *              )
     *          ),
     *          @SWG\Response(
     *              response=401,
     *              description="Unauthorized",
     *              @SWG\Header(
     *                  type="string",
     *                  header="Access-Control-Allow-Headers"
     *              ),
     *              @SWG\Header(
     *                  type="string",
     *                  header="Access-Control-Allow-Methods"
     *              ),
     *              @SWG\Header(
     *                  type="string",
     *                  header="Access-Control-Allow-Origin"
     *              )
     *          ),
     *          @SWG\Response(
     *              response=403,
     *              description="Forbidden",
     *              @SWG\Header(
     *                  type="string",
     *                  header="Access-Control-Allow-Headers"
     *              ),
     *              @SWG\Header(
     *                  type="string",
     *                  header="Access-Control-Allow-Methods"
     *              ),
     *              @SWG\Header(
     *                  type="string",
     *                  header="Access-Control-Allow-Origin"
     *              )
     *          ),
     *          x={"amazon-apigateway-integration": {
     *              "requestParameters" : {
     *                  "integration.request.header.Authorization": "method.request.header.Authorization",
     *                  "integration.request.path.itemId": "method.request.path.itemId",
     *                  "integration.request.path.externalReference": "method.request.path.externalReference"
     *              },
     *              "responses": {
     *                  "200": {
     *                      "statusCode": "200",
     *                      "responseParameters": {
     *                          "method.response.header.Access-Control-Allow-Headers": "'Content-Type,X-Amz-Date,Authorization,X-Api-Key'",
     *                          "method.response.header.Access-Control-Allow-Methods": "'GET'",
     *                          "method.response.header.Access-Control-Allow-Origin": "'*'"
     *                      }
     *                  },
     *                  "401": {
     *                      "statusCode": "401",
     *                      "responseParameters": {
     *                          "method.response.header.Access-Control-Allow-Headers": "'Content-Type,X-Amz-Date,Authorization,X-Api-Key'",
     *                          "method.response.header.Access-Control-Allow-Methods": "'GET'",
     *                          "method.response.header.Access-Control-Allow-Origin": "'*'"
     *                      }
     *                  },
     *                  "403": {
     *                      "statusCode": "403",
     *                      "responseParameters": {
     *                          "method.response.header.Access-Control-Allow-Headers": "'Content-Type,X-Amz-Date,Authorization,X-Api-Key'",
     *                          "method.response.header.Access-Control-Allow-Methods": "'GET'",
     *                          "method.response.header.Access-Control-Allow-Origin": "'*'"
     *                      }
     *                  }
     *              },
     *              "uri": "https://${stageVariables.host}/api/adhesion/unique/{itemId}/{externalReference}",
     *              "passthroughBehavior": "when_no_match",
     *              "httpMethod": "GET",
     *              "type": "http"
     *          }}
     *      )
     * )
     *
     * @param integer $itemId
     * @param string  $externalReference
     *
     * @return Response
     */
    public function isUniqueAction($itemId, $externalReference)
    {
        $userAccount = $this->getUser()->getUserAccount();
        $states = [AdhesionStateRepository::PENDING_TO_SIGN, AdhesionStateRepository::SIGNED];

        /** @var AdhesionRepository $bankAccountRepository */
        $bankAccountRepository = $this->getDoctrine()->getRepository('AppBundle:Adhesion');
        $unique = $bankAccountRepository->isUniqueByItemAndExternalReference($userAccount, $states, $itemId, $externalReference);

        return $this->createOkView([
            'item_id' => $itemId,
            'external_reference' => $externalReference,
            'unique' => $unique
        ]);
    }

    /**
     * Upload an adhesion file.
     *
     * @FOSRest\Post(
     *      "/adhesion/{id}/file",
     *      name="api_adhesion_file",
     *      options={"method_prefix"=false}
     * )
     *
     * @QueryParam(name="account", nullable=true, description="Secondary user account Id")
     *
     * @param Request $request
     * @param ParamFetcher $paramFetcher
     * @param string $id
     *
     * @return Response
     */
    public function fileAction(Request $request, ParamFetcher $paramFetcher, $id)
    {
        $userAccountId = $paramFetcher->get('account');
        $userAccount = $this->get('app.service.user_account_fetcher')->fetch($userAccountId);

        if (!$userAccount) {
            return $this->createForbiddenView();
        }

        /** @var Adhesion $adhesion */
        $adhesion = $this->getDoctrine()
            ->getRepository('AppBundle:Adhesion')
            ->findOneBy(['id' => $id, 'userAccount' => $userAccount]);

        if (!$adhesion) {
            return $this->createNotFoundView();
        }

        $form = $this->createForm(AdhesionFileType::class, $adhesion, [
            'validation_groups' => ['file']
        ]);

        $form->handleRequest($request);

        $this->denyAccessUnlessGranted(AdhesionVoter::UPLOAD_FILE_ACTION, $adhesion);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var AdhesionState $adhesionSignedState */
            $adhesionSignedState = $this->getDoctrine()
                ->getRepository('AppBundle:AdhesionState')
                ->find(AdhesionStateRepository::SIGNED);

            $adhesion->setState($adhesionSignedState);
            $em = $this->getDoctrine()->getManager();
            $em->persist($adhesion);
            $em->flush();

            return $this->createCreatedView($adhesion);
        }

        return $this->createBadRequestView($form);
    }

    /**
     * @SWG\Path(
     *      path="/adhesion/batch",
     *      @SWG\Options(
     *          tags={"CORS"},
     *          @SWG\Response(
     *              response=200,
     *              description="Ok",
     *              @SWG\Header(
     *                  type="string",
     *                  header="Access-Control-Allow-Headers"
     *              ),
     *              @SWG\Header(
     *                  type="string",
     *                  header="Access-Control-Allow-Methods"
     *              ),
     *              @SWG\Header(
     *                  type="string",
     *                  header="Access-Control-Allow-Origin"
     *              )
     *          ),
     *          x={"amazon-apigateway-integration": {
     *              "type": "mock",
     *              "requestTemplates": {
     *                  "application/json": "{'statusCode': 200}"
     *              },
     *              "responses": {
     *                  "default": {
     *                      "statusCode": "200",
     *                      "responseParameters": {
     *                          "method.response.header.Access-Control-Allow-Headers": "'Content-Type,X-Amz-Date,Authorization,X-Api-Key'",
     *                          "method.response.header.Access-Control-Allow-Methods": "'POST'",
     *                          "method.response.header.Access-Control-Allow-Origin": "'*'"
     *                      }
     *                  }
     *              }
     *          }}
     *      )
     * )
     */

    /**
     * Creates adhesions from a batch.
     *
     * @FOSRest\Post(
     *      "/adhesion/batch",
     *      name="api_adhesion_create_from_batch",
     *      options={"method_prefix"=false}
     * )
     *
     * @SWG\Path(
     *      path="/adhesion/batch",
     *      @SWG\Post(
     *          tags={"Adhesion"},
     *          summary="Creates adhesions from a batch",
     *          @SWG\Parameter(
     *              in="header",
     *              type="string",
     *              required=true,
     *              name="Authorization"
     *          ),
     *          @SWG\Response(
     *              response=201,
     *              description="Created",
     *              @SWG\Header(
     *                  type="string",
     *                  header="Access-Control-Allow-Headers"
     *              ),
     *              @SWG\Header(
     *                  type="string",
     *                  header="Access-Control-Allow-Methods"
     *              ),
     *              @SWG\Header(
     *                  type="string",
     *                  header="Access-Control-Allow-Origin"
     *              ),
     *              @SWG\Schema(
     *                  @SWG\Items(ref="#/definitions/Adhesion")
     *              )
     *          ),
     *          @SWG\Response(
     *              response=400,
     *              description="Bad Request",
     *              @SWG\Header(
     *                  type="string",
     *                  header="Access-Control-Allow-Headers"
     *              ),
     *              @SWG\Header(
     *                  type="string",
     *                  header="Access-Control-Allow-Methods"
     *              ),
     *              @SWG\Header(
     *                  type="string",
     *                  header="Access-Control-Allow-Origin"
     *              )
     *          ),
     *          @SWG\Response(
     *              response=401,
     *              description="Unauthorized",
     *              @SWG\Header(
     *                  type="string",
     *                  header="Access-Control-Allow-Headers"
     *              ),
     *              @SWG\Header(
     *                  type="string",
     *                  header="Access-Control-Allow-Methods"
     *              ),
     *              @SWG\Header(
     *                  type="string",
     *                  header="Access-Control-Allow-Origin"
     *              )
     *          ),
     *          @SWG\Response(
     *              response=403,
     *              description="Forbidden",
     *              @SWG\Header(
     *                  type="string",
     *                  header="Access-Control-Allow-Headers"
     *              ),
     *              @SWG\Header(
     *                  type="string",
     *                  header="Access-Control-Allow-Methods"
     *              ),
     *              @SWG\Header(
     *                  type="string",
     *                  header="Access-Control-Allow-Origin"
     *              )
     *          ),
     *          x={"amazon-apigateway-integration": {
     *              "requestParameters" : {
     *                  "integration.request.header.Authorization": "method.request.header.Authorization"
     *              },
     *              "responses": {
     *                  "default": {
     *                      "statusCode": "201",
     *                      "responseParameters": {
     *                          "method.response.header.Access-Control-Allow-Headers": "'Content-Type,X-Amz-Date,Authorization,X-Api-Key'",
     *                          "method.response.header.Access-Control-Allow-Methods": "'POST'",
     *                          "method.response.header.Access-Control-Allow-Origin": "'*'"
     *                      }
     *                  },
     *                  "400": {
     *                      "statusCode": "400",
     *                      "responseParameters": {
     *                          "method.response.header.Access-Control-Allow-Headers": "'Content-Type,X-Amz-Date,Authorization,X-Api-Key'",
     *                          "method.response.header.Access-Control-Allow-Methods": "'POST'",
     *                          "method.response.header.Access-Control-Allow-Origin": "'*'"
     *                      }
     *                  },
     *                  "401": {
     *                      "statusCode": "401",
     *                      "responseParameters": {
     *                          "method.response.header.Access-Control-Allow-Headers": "'Content-Type,X-Amz-Date,Authorization,X-Api-Key'",
     *                          "method.response.header.Access-Control-Allow-Methods": "'POST'",
     *                          "method.response.header.Access-Control-Allow-Origin": "'*'"
     *                      }
     *                  },
     *                  "403": {
     *                      "statusCode": "403",
     *                      "responseParameters": {
     *                          "method.response.header.Access-Control-Allow-Headers": "'Content-Type,X-Amz-Date,Authorization,X-Api-Key'",
     *                          "method.response.header.Access-Control-Allow-Methods": "'POST'",
     *                          "method.response.header.Access-Control-Allow-Origin": "'*'"
     *                      }
     *                  },
     *              },
     *              "uri": "https://${stageVariables.host}/api/adhesion/batch",
     *              "passthroughBehavior": "when_no_match",
     *              "httpMethod": "POST",
     *              "type": "http"
     *          }}
     *      )
     * )
     *
     * @param Request $request
     *
     * @return Response
     */
    public function createFromBatchAction(Request $request)
    {
        $userAccount = $this->getUser()->getUserAccount();

        /** @var AdhesionState $pendingToSignState */
        $state = $this->getDoctrine()
            ->getRepository('AppBundle:AdhesionState')
            ->find(AdhesionStateRepository::PENDING_TO_SIGN);

        $form = $this->createForm(AdhesionBatchType::class, null, [
            'state' => $state,
            'user_account' => $userAccount,
            'validation_groups' => ['create']
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $adhesions = $form->getData()['adhesion'];

            /** @var Adhesion $adhesion */
            foreach ($adhesions as $adhesion) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($adhesion);
                $em->flush();

                /** @var AppMailer $appMailer */
                $appMailer = $this->get('app.mailer');
                $adhesionMailer = $appMailer->getAdhesionMailer();

                $signingToken = $adhesion->getSigningToken();
                $uri = 'http:'.$this->generateUrl('user_action_adhesion_sign', ['signingToken' => $signingToken], UrlGeneratorInterface::NETWORK_PATH);
                $adhesionMailer->sendSignatureRequest($adhesion, $uri);
            }

            return $this->createCreatedView($form->getData());
        }

        return $this->createBadRequestView($form);
    }
}
